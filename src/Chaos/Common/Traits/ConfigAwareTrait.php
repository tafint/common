<?php namespace Chaos\Common\Traits;

use Noodlehaus\ConfigInterface;
use Chaos\Common\Classes\Config;

/**
 * Trait ConfigAwareTrait
 * @author ntd1712
 */
trait ConfigAwareTrait
{
    /** @var ConfigInterface */
    private static $__config__;

    /**
     * Get a reference to the global configuration object. The object returned will be of type <tt>ConfigInterface</tt>
     *
     * @return  ConfigInterface
     */
    public function getConfig()
    {
        return self::$__config__;
    }

    /**
     * Set a reference to the global configuration object if it does not exist
     *
     * @param   array|string|ConfigInterface $config
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