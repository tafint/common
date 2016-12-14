<?php namespace Chaos\Common\Types;

/**
 * Class SimpleArrayType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\SimpleArrayType
 */
class SimpleArrayType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value)
        {
            return [];
        }
        elseif (is_object($value) || is_array($value))
        {
            return (array)$value;
        }

        if (is_resource($value))
        {
            $value = stream_get_contents($value);
        }

        return explode(',', $value);
    }
}