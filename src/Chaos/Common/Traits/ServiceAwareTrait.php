<?php namespace Chaos\Common\Traits;

/**
 * Trait ServiceAwareTrait
 * @author ntd1712
 *
 * @method \Noodlehaus\ConfigInterface getConfig()
 * @method \League\Container\ContainerInterface getContainer()
 */
trait ServiceAwareTrait
{
    /** @var array */
    private static $__services__ = [];

    /**
     * Get a reference to the service object. The object returned will be of type <tt>IBaseService</tt>
     *  $this->getService()->...
     *  $this->getService('User')->...
     *  $this->getService('Account\Services\UserService')->...
     *
     * @param   string $name The service name; defaults to get_called_class()
     * @param   boolean $cache; defaults to TRUE
     * @return  mixed|\Chaos\Common\AbstractBaseService|\Chaos\Common\IBaseService
     */
    public function getService($name = null, $cache = true)
    {
        if (empty($name) || false === strpos($name, '\\'))
        {
            $serviceName = preg_replace(CHAOS_REPLACE_CLASS_SUFFIX, '$1', $name ?: get_called_class()) . 'Service';
        }
        else
        {
            $serviceName = $name;
        }

        if ($cache && isset(self::$__services__[$serviceName]))
        {
            return self::$__services__[$serviceName];
        }

        return self::$__services__[$serviceName] = $this->getContainer()->get($serviceName)
            ->setContainer($this->getContainer())
            ->setConfig($this->getConfig());
    }
}