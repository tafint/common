<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class EnumType
 * @author ntd1712
 */
class EnumType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value)
        {
            return null;
        }

        if (is_string($value) && class_exists($value, false))
        {
            return (new \ReflectionClass($value))->newInstanceWithoutConstructor();
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