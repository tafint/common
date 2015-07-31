<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class SimpleArrayType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\SimpleArrayType
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

        if (is_object($value) || is_array($value))
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        return explode(',', $value);
    }
}