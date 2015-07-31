<?php namespace Chaos\Common\Types;

/**
 * Class NullType
 * @author ntd1712
 */
class NullType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        return (unset)$value;
    }
}