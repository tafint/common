<?php namespace Chaos\Common;

use Doctrine\ORM\Events;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

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
     *
     * @param   array|string $config The path to the config file
     * @param   array|\ArrayAccess $di
     */
    public function __construct($config = [], $di = [])
    {
        $this->setConfig($config)
             ->setContainer(['di' => self::$cache['__aliases__'] = $di]) /** @example http://goo.gl/P3e7ct */
             ->getContainer()->singleton(DOCTRINE_ENTITY_MANAGER, app(DOCTRINE_ENTITY_MANAGER));
    }

    /** {@inheritdoc} @return array|mixed */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        $request = $this->getRouter()->getCurrentRequest();

        return isset($key) ? $request->get($key, $default, $deep) : $request->all() + [
            'ModifiedAt' => 'now',
            'ModifiedBy' => \Session::get('loggedName'),
            'IsDeleted' => false,
            'ApplicationKey' => $this->getConfig('appKey')
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

        if (isset(self::$cache[$serviceName]))
        {
            return self::$cache[$serviceName];
        }

        self::$cache[$serviceName] = $this->getContainer($serviceName)
            ->setContainer($this->getContainer())
            ->setConfig($this->getConfig());

        /**
         * {@inheritdoc} @return IBaseRepository
         *  $this->getService('User')->getRepository('User')->...
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

            if (isset(self::$cache[$repositoryName]))
            {
                return self::$cache[$repositoryName];
            }

            self::$cache[$repositoryName] = $this->getContainer(DOCTRINE_ENTITY_MANAGER)
                ->getRepository(@self::$cache['__aliases__'][$name]['definition'] ?: $name)
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

        return self::$cache[$serviceName];
    }

    /**
     * Get the <tt>user</tt> instance
     *
     * @param   string $token The JWT token; defaults to JWTAuth::getToken()
     * @return  IBaseEntity
     * @throws  JWTException
     */
    protected function getUser($token = null)
    {
        if (null === ($user = \Session::get('loggedUser')))
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