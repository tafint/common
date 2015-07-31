<?php namespace Chaos\Common;

/**
 * Interface IBaseEntity
 * @author ntd1712
 */
interface IBaseEntity extends IBaseObjectItem
{
    /**
     * Add a rule to selected property
     *
     * @param   \ReflectionProperty $property
     * @param   string $rule
     * @return  $this
     */
    function addRule($property, $rule);
    /**
     * Get the entity identifier
     *
     * @return  array
     */
    function getIdentifier();
    /**
     * Set the entity identifier
     *
     * @param   array $identifier
     * @return  IBaseEntity|mixed
     */
    function setIdentifier(array $identifier);
    /**
     * Whether the entity is in valid status
     * and should continue its normal method execution cycles
     *
     * @return  bool|array An array of errors, FALSE otherwise
     */
    function validate();
}