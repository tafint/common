<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class JsonArrayType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\JsonArrayType
 */
class JsonArrayType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value || '' === $value)
        {
            return [];
        }

        if (is_object($value) || is_array($value))
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        return json_decode($value, true);
    }
}