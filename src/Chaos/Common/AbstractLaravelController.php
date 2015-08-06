<?php namespace Chaos\Common;

use Doctrine\ORM\Events;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use League\Container\Container;
use Tymon\JWTAuth\Exceptions\JWTException;

define('MODULE_PATH', app()->basePath() . DIRECTORY_SEPARATOR . 'modules');

/**
 * Class AbstractLaravelController
 * @author ntd1712
 */
abstract class AbstractLaravelController extends Controller
{
    use BaseControllerTrait, Traits\ConfigAwareTrait, Traits\ContainerAwareTrait,
        DispatchesCommands, ValidatesRequests;

    /**
     * Constructor
     * @todo    Reorganize the aliases
     */
    public function __construct()
    {
        $this->setConfig(Classes\Config::load(is_readable($path = MODULE_PATH . '/config.params.php') ? $path : []))
             ->setContainer(new Container(self::$cache['__aliases__'] = require_once __DIR__ . '/../aliases.php'))
             ->getContainer()->singleton(DOCTRINE_ENTITY_MANAGER, app(DOCTRINE_ENTITY_MANAGER));
    }

    /**
     * The default "postLogin" action, you can override this in derived class
     *
     * @param   \Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function postLogin(\Request $request)
    {
        try
        {
            // are we logging out?
            if (true === (bool)$request::get('logout'))
            {
                \Session::remove('user');
                return ['success' => \JWTAuth::invalidate(\JWTAuth::getToken())];
            }

            // hell no!
            $validator = $this->getValidationFactory()->make($request::all(), [
                'email' => 'required|email|max:255', 'password' => 'required'
            ]);

            if (!$validator->fails())
            {   /** @var \Account\Entities\User $entity */
                $entity = $this->getService('User')->getRepository()->findOneBy(['Email' => $request::get('email')]);
            }

            if (!isset($entity) || !\Hash::check($request::get('password'), $entity->getPassword()) ||
               ($this->getConfig('superUserId') != $entity->getId() &&
                $this->getConfig('appKey') !== $entity->getApplicationKey()))
            {
                throw new JWTException('Invalid credentials', 418);
            }

            $token = \JWTAuth::setIdentifier('Id')->fromUser($entity);
            \Session::put('user', clone $entity);

            // prepare data for output
            $user = $entity->toSimpleArray();
            $user['Roles'] = $user['Permissions'] = [];
            unset($user['UserRoles']);

            if (0 !== count($userRoles = $entity->getUserRoles()))
            {   /** @var \Account\Entities\UserRole $userRole */
                foreach ($userRoles as $userRole)
                {
                    $user['Roles'][strtolower($userRole->getRole()->getName())] = $userRole->getRole()->getId();

                    if (0 !== count($permissions = $userRole->getRole()->getPermissions()))
                    {   /** @var \Account\Entities\Permission $permission */
                        foreach ($permissions as $permission)
                        {
                            $user['Permissions'][strtolower($permission->getName())] = $permission->getId();
                        }
                    }
                }
            }

            // bye!
            return compact('token', 'user');
        }
        catch (JWTException $e)
        {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /** {@inheritdoc} @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        $request = $this->getRouter()->getCurrentRequest();
        $user = \Session::get('user');

        return isset($key) ? $request->get($key, $default, $deep) : $request->all() + [
            'ModifiedAt' => 'now',
            'ModifiedBy' => isset($user) ? $user->getUsername() : null,
            'IsDeleted' => false
        ];
    }

    /**
     * {@inheritdoc} @return IBaseService
     *  $this->getService()->...
     *  $this->getService('User')->...
     *  $this->getService('Account\Service\UserService')->...
     */
    protected function getService($name = null)
    {
        if (empty($name) || false === strpos($name, '\\'))
        {
            $serviceName = preg_replace(CHAOS_REPLACE_CLASS_SUFFIX, '$1', $name ?: get_called_class()) . 'Service';
        }
        else
        {
            $serviceName = $name;
        }

        if (!isset(self::$cache[$serviceName]))
        {
            self::$cache[$serviceName] = $this->getContainer($serviceName)
                ->setContainer($this->getContainer())
                ->setConfig($this->getConfig());

            /**
             * {@inheritdoc} @return IBaseRepository
             *  $this->getService()->getRepository()->...
             *  $this->getService('User')->getRepository('Role')->...
             *  $this->getService('Account\Service\UserService')->getRepository('Account\Entities\Role')->...
             */
            self::$cache[$serviceName]->getRepository = function($name = null) use($serviceName)
            {
                if (empty($name) || false === strpos($name, '\\'))
                {
                    $name = preg_replace(CHAOS_REPLACE_CLASS_SUFFIX, '$1', $name ?: $serviceName);
                    $repositoryName = $name . 'Repository';
                }
                else
                {
                    $repositoryName = $name;
                }

                if (!isset(self::$cache[$repositoryName]))
                {
                    self::$cache[$repositoryName] = $this->getContainer(DOCTRINE_ENTITY_MANAGER)
                        ->getRepository(@self::$cache['__aliases__']['di'][$name] ?: $name)
                        ->setContainer($this->getContainer())
                        ->setConfig($this->getConfig());

                    // inject some stuffs into the entity
                    foreach (self::$cache[$repositoryName]->metadata->entityListeners as $k => $v)
                    {
                        if (Events::postLoad === $k)
                        {
                            foreach ($v as $listener)
                            {
                                self::$cache[$repositoryName]->entityManager->getConfiguration()
                                ->getEntityListenerResolver()->register(
                                    $this->getContainer($listener['class'])
                                         ->setContainer($this->getContainer())
                                         ->setConfig($this->getConfig())
                                );
                            }
                        }
                    }
                }

                return self::$cache[$repositoryName];
            };

            /** {@inheritdoc} A service can call another service when appropriate */
            self::$cache[$serviceName]->getService = function($name = null)
            {
                return $this->getService($name);
            };

            /** {@inheritdoc} */
            self::$cache[$serviceName]->getUser = function($token = null)
            {
                return $this->getUser($token);
            };
        }

        return self::$cache[$serviceName];
    }

    /**
     * Get the <tt>User</tt> instance
     *
     * @param   string $token The JWT token; defaults to JWTAuth::getToken()
     * @return  IBaseEntity
     * @throws  JWTException
     */
    protected function getUser($token = null)
    {
        if (null === ($user = \Session::get('user')))
        {
            $payload = \JWTAuth::getPayload($token ?: \JWTAuth::getToken());
            $user = $this->getService('User')->getRepository()->find($payload['sub']);

            if (null === $user)
            {
                throw new JWTException('User not found', 404);
            }
        }

        return $user;
    }

    /** @var array */
    private static $cache = ['__aliases__' => null];
}