<?php namespace Chaos\Common;

use Doctrine\ORM\Events;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use League\Container\Container;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

define('MODULE_PATH', app()->basePath() . '/modules');

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
     */
    public function __construct()
    {
        $this->setConfig(Classes\Config::load(is_readable($path = MODULE_PATH . '/config.params.php') ? $path : []));
        $this->setContainer(new Container(self::$cache['__aliases__'] = require_once __DIR__ . '/../aliases.php'))
             ->getContainer()->singleton(DOCTRINE_ENTITY_MANAGER, app(DOCTRINE_ENTITY_MANAGER));
    }

    /**
     * Handle a login request to the application
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
                return ['success' => JWTAuth::invalidate(JWTAuth::getToken())];
            }

            // hell no!
            $validator = $this->getValidationFactory()->make($request::all(), [
                'email' => 'required|email|max:255', 'password' => 'required'
            ]);

            if ($validator->fails() || !$token = JWTAuth::attempt($request::only('email', 'password')))
            {
                return response()->json(['error' => 'Invalid credentials'], 418);
            }

            $user = $this->getUser($token);

            // prepare data for output
            $userRoles = clone $user->UserRoles;
            $user = $user->toSimpleArray();
            $user['Roles'] = $user['Permissions'] = [];

            if (0 !== count($userRoles))
            {
                foreach ($userRoles as $userRole)
                {
                    $user['Roles'][strtolower($userRole->Role->Name)] = $userRole->Role->Id;

                    if (0 !== count($userRole->Role->Permissions))
                    {
                        foreach ($userRole->Role->Permissions as $permission)
                        {
                            $user['Permissions'][strtolower($permission->Name)] = $permission->Id;
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
            'ModifiedBy' => isset($user) ? $user->Username : null,
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

            /** {@inheritdoc} */
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

    /** {@inheritdoc} @return IBaseEntity */
    protected function getUser($token = null)
    {
        if (null === ($user = \Session::get('user')))
        {
            $payload = JWTAuth::getPayload($token ?: JWTAuth::getToken());
            $user = $this->getService('User')->getRepository()->find($payload['sub']);

            if (null === $user || (1 !== $payload['sub'] && $this->getConfig('appKey') !== $user->getApplicationKey()))
            {
                throw new JWTException('User not found', 404);
            }

            \Session::put('user', clone $user);
        }

        return $user;
    }

    /** @var array */
    private static $cache = ['__aliases__' => null];
}