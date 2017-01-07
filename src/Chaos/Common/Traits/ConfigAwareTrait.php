<?php namespace Chaos\Common\Traits;

use M1\Vars\Vars;

/**
 * Trait ConfigAwareTrait
 * @author ntd1712
 */
trait ConfigAwareTrait
{
    /** @var Vars */
    private static $__config__;

    /**
     * Get a reference to the configuration object. The object returned will be of type <tt>Vars</tt>
     *
     * @return  Vars
     */
    public function getConfig()
    {
        return self::$__config__;
    }

    /**
     * Set a reference to the configuration object
     *
     * @param   array|\ArrayAccess|Vars $config Either be an array holding the path to the config files or a Vars object
     * @return  $this
     */
    public function setConfig($config)
    {
        if (!$config instanceof Vars)
        {
            $options = isset($config['__options__']) ? $config['__options__'] : [];
            unset($config['__options__']);

            array_unshift($config, __DIR__ . '/../../settings.yml');
            $config = new Vars($config, $options);

            if (isset($options['__framework__']))
            {
                foreach ($options['__framework__'] as $k => $v)
                {
                    is_array($v) && $config->arrayKeyExists($k)
                        ? $config->set($k, array_replace_recursive($config->get($k), $v))
                        : $config->set($k, $v);
                }
            }
        }

        self::$__config__ = $config;
        return $this;
    }
}