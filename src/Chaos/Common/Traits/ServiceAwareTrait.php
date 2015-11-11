<?php namespace Chaos\Common\Traits;

/**
 * Trait ServiceAwareTrait
 * @author ntd1712
 *
 * @method mixed|\Noodlehaus\ConfigInterface getConfig(string $key = null, $default = null)
 * @method mixed|\League\Container\ContainerInterface getContainer(string $alias = null, array $args = [])
 */
trait ServiceAwareTrait
{
    /** @var array */
    private static $services = [];

    /**
     * Get the <tt>service</tt> instance
     *  $this->getService()->...
     *  $this->getService('User')->...
     *  $this->getService('Account\Services\UserService')->...
     *
     * @param   string $name The service name; defaults to get_called_class()
     * @param   bool $cache; defaults to TRUE
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

        if (isset(self::$services[$serviceName]) && $cache)
        {
            return self::$services[$serviceName];
        }

        return self::$services[$serviceName] = $this
            ->getContainer($serviceName)
            ->setContainer($this->getContainer())
            ->setConfig($this->getConfig());
    }
}