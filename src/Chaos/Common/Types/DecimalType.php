<?php namespace Chaos\Common\Types;

/**
 * Class DecimalType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\DecimalType
 */
class DecimalType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        return null === $value ? null : $value;
    }
}