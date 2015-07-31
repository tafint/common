<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class BinaryType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\BinaryType
 */
class BinaryType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value)
        {
            return null;
        }

        if (is_string($value))
        {
            $stream = fopen('php://memory', 'r+b');
            fwrite($stream, $value);
            rewind($stream);
            $value = $stream;
        }

        if (!is_resource($value))
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $value;
    }
}