<?php namespace Chaos\Common\Exceptions;

/**
 * Class ConversionException
 * @see Doctrine\DBAL\Types\ConversionException
 */
class ConversionException extends \Exception implements IException
{
    /**
     * Thrown when a Database to Type Conversion fails
     *
     * @param   string $value
     * @param   string $toType
     * @return  ConversionException
     */
    public static function conversionFailed($value, $toType)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;
        return new self('Could not convert value "' . $value . '" to type ' . $toType);
    }

    /**
     * Thrown when a Database to Type Conversion fails and we can make a statement
     * about the expected format
     *
     * @param   string $value
     * @param   string $toType
     * @param   string $expectedFormat
     * @return  ConversionException
     */
    public static function conversionFailedFormat($value, $toType, $expectedFormat)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;
        return new self('Could not convert value "' . $value . '" to type ' . $toType .
            '. Expected format: ' . $expectedFormat);
    }

    /**
     * @param   string $name
     * @return  ConversionException
     */
    public static function unknownColumnType($name)
    {
        return new self('Unknown column type "' . $name . '" requested');
    }
}