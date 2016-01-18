<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class ObjectType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\ObjectType
 */
class ObjectType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_object($value))
        {
            return $value;
        }

        if (is_resource($value))
        {
            $value = stream_get_contents($value);
        }

        $val = @unserialize($value);

        if (false === $val && 'b:0;' !== $value)
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}