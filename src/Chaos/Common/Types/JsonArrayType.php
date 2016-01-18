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
        elseif (is_object($value) || is_array($value))
        {
            return $value;
        }

        if (is_resource($value))
        {
            $value = stream_get_contents($value);
        }

        return json_decode($value, true);
    }
}