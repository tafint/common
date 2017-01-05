<?php namespace Chaos\Common\Traits;

/**
 * Trait ServiceAwareTrait
 * @author ntd1712
 *
 * @method \Noodlehaus\ConfigInterface getConfig()
 * @method \Symfony\Component\DependencyInjection\ContainerBuilder getContainer()
 */
trait ServiceAwareTrait
{
    /** @var array */
    private static $__services__ = [];

    /**
     * Get a reference to the service object. The object returned will be of type <tt>IBaseService</tt>
     *  $this->getService()->...
     *  $this->getService('User')->...
     *
     * @param   string $name The service name; defaults to get_called_class()
     * @param   boolean $cache; defaults to TRUE
     * @return  mixed|\Chaos\Common\AbstractBaseService|\Chaos\Common\IBaseService
     */
    public function getService($name = null, $cache = true)
    {
        if (empty($name))
        {
            $serviceName = str_replace(['Controller', 'Service'], '', trim(strrchr(get_called_class(), '\\'), '\\'))
                . 'Service';
        }
        elseif (false === strpos($name, '\\'))
        {
            $serviceName = str_replace('Service', '', $name) . 'Service';
        }
        else
        {
            $serviceName = $name;
        }

        if (isset(self::$__services__[$serviceName]) && $cache)
        {
            return self::$__services__[$serviceName];
        }

        return self::$__services__[$serviceName] = $this->getContainer()->get($serviceName)
            ->setContainer($this->getContainer())
            ->setConfig($this->getConfig());
    }
}