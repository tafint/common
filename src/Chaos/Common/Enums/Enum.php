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
        return static::$map;
    }

    /**
     * Check if a constant exists
     *
     * @param   string $name
     * @return  boolean
     */
    public static function has($name)
    {
        return isset(static::$map[$name]);
    }

    /** Private constructor */
    final private function __construct() {}
}