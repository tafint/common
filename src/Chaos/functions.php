<?php

if (!function_exists('array_column'))
{
    /**
     * @param   array $input
     * @param   mixed $columnKey
     * @param   mixed $indexKey
     * @return  array
     * @link    https://goo.gl/xov2uO
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2)
        {
            trigger_error("array_column() expects at least 2 parameters, $argc given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0]))
        {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }

        if (!(is_int($params[1]) || is_float($params[1]) || is_string($params[1])) && null !== $params[1] &&
            !(is_object($params[1]) && method_exists($params[1], '__toString')))
        {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2]) && !(is_int($params[2]) || is_float($params[2]) || is_string($params[2])) &&
            !(is_object($params[2]) && method_exists($params[2], '__toString')))
        {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = null !== $params[1] ? (string)$params[1] : null;
        $paramsIndexKey = null;

        if (isset($params[2]))
        {
            if (is_float($params[2]) || is_int($params[2]))
            {
                $paramsIndexKey = (int)$params[2];
            }
            else
            {
                $paramsIndexKey = (string)$params[2];
            }
        }

        $resultArray = [];

        foreach ($paramsInput as $row)
        {
            $key = $value = null;
            $keySet = $valueSet = false;

            if (null !== $paramsIndexKey && array_key_exists($paramsIndexKey, $row))
            {
                $keySet = true;
                $key = (string)$row[$paramsIndexKey];
            }

            if (null === $paramsColumnKey)
            {
                $valueSet = true;
                $value = $row;
            }
            elseif (is_array($row) && array_key_exists($paramsColumnKey, $row))
            {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet)
            {
                if ($keySet)
                {
                    $resultArray[$key] = $value;
                }
                else
                {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }
}

if (!function_exists('json_last_error_msg'))
{
    /**
     * @return  string
     * @link    https://goo.gl/MK9HSY
     */
    function json_last_error_msg()
    {
        static $errors = [
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX => 'Syntax error',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        ];
        $error = json_last_error();

        return isset($errors[$error]) ? $errors[$error] : 'Unknown error (code ' . $error . ')';
    }
}

if (!function_exists('is_defined'))
{
    /**
     * @param   string $var
     * @return  bool
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
     * @return  bool
     */
    function is_blank($var)
    {
        return null === $var || '' === $var || (is_string($var) && ctype_space($var));
    }
}

if (!function_exists('is_json'))
{
    /**
     * @return  bool|mixed
     * @link    https://goo.gl/fmYXIR
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
     * @param   array $matches
     * @return  array
     * @link    https://goo.gl/KguDuz
     */
    function _unicode_caseflip($matches)
    {
        return $matches[0][0] . chr(ord($matches[0][1]) ^ 32);
    }

    /**
     * @return  array
     * @link    https://goo.gl/remn9g
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
            $uuid = extension_loaded('mbstring') ? mb_strtolower($uuid) :
                preg_replace_callback('/\xC3[\x80-\x96\x98-\x9E]/', '_unicode_caseflip', strtolower($uuid));
        }
        else
        {
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        return $uuid;
    }
}