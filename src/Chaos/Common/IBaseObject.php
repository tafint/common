<?php namespace Chaos\Common;

/**
 * Interface IBaseObject
 * @author ntd1712
 */
interface IBaseObject
{
    /**
     * Cast a JSON string to a result set
     *
     * @param   string $json The <i>json</i> string being decoded
     * @param   bool $assoc When <b>TRUE</b>, returned objects will be converted into associative arrays
     * @return  mixed The value encoded in <i>JSON</i> in appropriate PHP type
     * @throws  Exceptions\RuntimeException
     * @see     json_decode
     */
    function fromJson($json, $assoc = false);
    /**
     * Cast a result set to a JSON string
     *
     * @return  string A <i>JSON</i> encoded string on success or <b>FALSE</b> on failure
     * @throws  Exceptions\RuntimeException
     * @see     json_encode
     */
    function toJson();
    /**
     * Recursively cast a result set to an array
     *
     * @return  array
     */
    function toArray();
}