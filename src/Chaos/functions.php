<?php

if (!function_exists('array_unset'))
{
    /**
     * <code>
     * array_unset($array, 'baz.foo.boo');
     * </code>
     *
     * @param   array $array
     * @param   string $path
     * @return  array
     * @see     http://goo.gl/aSmCgb
     */
    function array_unset(array &$array, $path)
    {
        $pieces = explode('.', $path);
        $count = count($pieces) - 1;
        $i = 0;

        while ($i < $count)
        {
            $piece = $pieces[$i];

            if (!array_key_exists($piece, $array))
            {
                return null;
            }

            $array = &$array[$piece];
            $i++;
        }

        $piece = end($pieces);
        unset($array[$piece]);

        return $array;
    }
}

if (!function_exists('is_defined'))
{
    /**
     * <code>
     * is_defined('UUID_TYPE_DEFAULT', 0);
     * </code>
     *
     * @param   string $var
     * @return  boolean
     */
    function is_defined($var)
    {
        $defined = defined($var);
        $args = func_get_args();

        if (!$defined && isset($args[1]))
        {
            return define($var, $args[1]);
        }

        return $defined;
    }
}

if (!function_exists('is_blank'))
{
    /**
     * @param   string $var
     * @return  boolean
     */
    function is_blank($var)
    {
        return null === $var || '' === $var || (is_string($var) && ctype_space($var));
    }
}

if (!function_exists('is_json'))
{
    /**
     * @return  boolean|mixed
     */
    function is_json()
    {
        $json = @call_user_func_array('json_decode', func_get_args());
        return JSON_ERROR_NONE === json_last_error() ? $json : false;
    }
}

if (!function_exists('shorten'))
{
    /**
     * @param   string $var
     * @return  string
     */
    function shorten($var)
    {
        $parts = explode('\\', $var);
        return end($parts);
    }
}