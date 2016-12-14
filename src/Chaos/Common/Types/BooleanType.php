<?php namespace Chaos\Common\Types;

/**
 * Class BooleanType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\BooleanType
 */
class BooleanType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        static $literals = ['f', 'false', 'n', 'no', 'off'];

        if (is_string($value) && in_array(strtolower($value), $literals, true))
        {
            return false;
        }

        return null === $value ? null : (bool)$value;
    }
}