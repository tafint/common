<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class DateType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\DateType
 */
class DateType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTime)
        {
            return $value;
        }

        $format = @func_get_arg(1) ?: 'Y-m-d';
        $val = \DateTime::createFromFormat('!' . $format, $value);

        if (!$val)
        {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}