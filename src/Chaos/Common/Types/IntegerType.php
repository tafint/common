<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class IntegerType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\IntegerType
 */
class IntegerType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (is_object($value))
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return null === $value ? null : (int)$value;
    }
}