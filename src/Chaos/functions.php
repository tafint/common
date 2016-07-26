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

if (!function_exists('uuid'))
{
    /**
     * @internal
     * @param   array $matches
     * @return  array
     * @see     https://goo.gl/KguDuz
     */
    function _unicode_caseflip($matches)
    {
        return $matches[0][0] . chr(ord($matches[0][1]) ^ 32);
    }

    /**
     * @return  array
     * @see     https://goo.gl/remn9g
     */
    function uuid()
    {
        if (function_exists('uuid_create') && !function_exists('uuid_make'))
        {
            $uuid = uuid_create(UUID_TYPE_DEFAULT);
        }
        elseif (function_exists('com_create_guid'))
        {
            $uuid = trim(com_create_guid(), '{}');
            $uuid = extension_loaded('mbstring') ? mb_strtolower($uuid)
                : preg_replace_callback('/\xC3[\x80-\x96\x98-\x9E]/', '_unicode_caseflip', strtolower($uuid));
        }
        else
        {
            $uuid = sprintf('%04x%04x-%04x-4%03x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 65535), mt_rand(0, 65535),
                // 16 bits for "time_mid"
                mt_rand(0, 65535),
                // 12 bits after the 0100 of (version) 4 for "time_hi_and_version"
                mt_rand(0, 4095),
                bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '10', 0, 2)),
                // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
                // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
                // 8 bits for "clk_seq_low" 48 bits for "node".
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
            );
        }

        return $uuid;
    }
}