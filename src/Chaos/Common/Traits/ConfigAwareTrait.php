<?php namespace Chaos\Common\Traits;

use Noodlehaus\ConfigInterface,
    Chaos\Common\Classes\Config;

/**
 * Trait ConfigAwareTrait
 * @author ntd1712
 */
trait ConfigAwareTrait
{
    /** @var ConfigInterface */
    private static $__config__;

    /**
     * Get a reference to the configuration object. The object returned will be of type <tt>ConfigInterface</tt>
     *
     * @return  ConfigInterface
     */
    public function getConfig()
    {
        return self::$__config__;
    }

    /**
     * Set a reference to the configuration object
     *
     * @param   array|string|ConfigInterface $config Either be a path to the config file,
     *          an array holding the configuration or a ConfigInterface object
     * @return  $this
     */
    public function setConfig($config)
    {
        if (!$config instanceof ConfigInterface)
        {
            $config = new Config($config);
        }

        self::$__config__ = $config;
        return $this;
    }
}