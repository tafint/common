<?php namespace Chaos\Common;

/**
 * Interface IBaseObject
 * @author ntd1712
 */
interface IBaseObject
{
    /**
     * Cast JSON string to result set
     *
     * @param   string $json <i>JSON</i> to be copied
     * @param   bool $assoc When <b>TRUE</b>, returned objects will be converted into associative arrays
     * @param   int $options Bitmask of <i>JSON</i> decode options
     * @return  mixed Value encoded in <i>JSON</i> in appropriate PHP type
     * @throws  Exceptions\RuntimeException
     */
    function fromJson($json, $assoc = true, $options = 0);
    /**
     * Cast result set to JSON string
     *
     * @param   int $options Bitmask of <i>JSON</i> encode options,
     *          the default is JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
     * @return  string <i>JSON</i> encoded string on success or <b>FALSE</b> on failure
     * @throws  Exceptions\RuntimeException
     */
    function toJson($options = 15);
    /**
     * Recursively cast result set to array
     *
     * @return  array
     */
    function toArray();
}