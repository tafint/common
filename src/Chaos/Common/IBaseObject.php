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
     * @param   string $json <p>The <i>json</i> string being decoded</p>
     * @param   bool $assoc [optional] <p>When <b>TRUE</b>, returned objects will be converted into associative arrays</p>
     * @return  mixed The value encoded in <i>json</i> in appropriate PHP type
     * @throws  Exceptions\RuntimeException
     * @see     json_decode
     */
    function fromJson($json, $assoc = false);
    /**
     * Cast a result set to a JSON string
     *
     * @return  string A <i>json</i> encoded string on success or <b>FALSE</b> on failure
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