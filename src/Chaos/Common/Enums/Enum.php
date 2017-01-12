<?php namespace Chaos\Common\Enums;

/**
 * Class Enum
 * @author ntd1712
 */
abstract class Enum
{
    /** @var array */
    protected static $map = [];

    /**
     * Return all of the constants
     *
     * @return  array
     */
    public static function all()
    {
        return array_keys(static::$map);
    }

    /**
     * Check if a constant exists
     *
     * @param   string $name
     * @return  boolean
     */
    final public static function has($name)
    {
        return isset(static::$map[$name]) || in_array($name, static::$map, true);
    }

    /** Private constructor */
    final private function __construct() {}
}