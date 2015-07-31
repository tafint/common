<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class ArrayType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\ArrayType
 */
class ArrayType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_array($value))
        {
            return $value;
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;
        $val = @unserialize($value);

        if (false === $val && 'b:0;' !== $value)
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}