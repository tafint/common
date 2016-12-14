<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class FloatType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\FloatType
 */
class FloatType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (is_object($value))
        {
            throw ConversionException::conversionFailed(gettype($value), $this);
        }

        return null === $value ? null : (float)$value;
    }
}