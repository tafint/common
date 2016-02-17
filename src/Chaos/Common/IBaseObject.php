<?php namespace Chaos\Common;

/**
 * Interface IBaseObject
 * @author ntd1712
 */
interface IBaseObject
{
    /**
     * Decode a JSON string
     *
     * @param   string $json <p>The <i>JSON</i> string being decoded</p>
     * @param   boolean $assoc [optional] <p>When <b>TRUE</b>, returned objects will be converted into associative arrays</p>
     * @return  mixed The value encoded in <i>JSON</i> in appropriate PHP type
     * @throws  Exceptions\RuntimeException
     * @see     json_decode
     */
    function fromJson($json, $assoc = false);
    /**
     * Return the JSON representation of a value
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