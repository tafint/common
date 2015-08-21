<?php namespace Chaos\Common;

/**
 * Interface IBaseObjectItem
 * @author ntd1712
 *
 * @method \ReflectionClass getReflection() Get the <tt>ReflectionClass</tt> instance of the object
 */
interface IBaseObjectItem extends IBaseObject
{
    /**
     * Cast a result set to array
     *
     * @return  array
     */
    function toSimpleArray();
    /**
     * Copy data from the passed in array to current object properties
     *
     * @param   array $data An array of key/value pairs to copy
     * @param   IBaseObjectItem $instance The last visited instance
     * @return  $this
     */
    function exchangeArray(array $data, IBaseObjectItem $instance = null);
    /**
     * Copy data from the passed in object to current object properties
     *
     * @param   object $data
     * @param   bool $force
     * @return  $this
     */
    function exchangeObject($data, $force = false);
}