<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class BigIntType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\BigIntType
 */
class BigIntType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (is_object($value) || is_array($value))
        {
            throw ConversionException::conversionFailed(gettype($value), $this);
        }

        return null === $value ? null : (string)$value;
    }
}