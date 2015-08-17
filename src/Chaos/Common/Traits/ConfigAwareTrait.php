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
    private static $config;

    /**
     * Either get a configuration setting or the <tt>Config</tt> instance
     *
     * @param   string $key
     * @param   mixed $default
     * @return  ConfigInterface|mixed
     */
    public function getConfig($key = null, $default = null)
    {
        return isset($key) ? self::$config->get($key, $default) : self::$config;
    }

    /**
     * Set a <tt>Config</tt> instance
     *
     * @param   ConfigInterface|array|string $config
     * @return  $this
     */
    public function setConfig($config)
    {
        if (!$config instanceof ConfigInterface)
        {
            $config = new Config($config);
        }

        self::$config = $config;
        return $this;
    }
}