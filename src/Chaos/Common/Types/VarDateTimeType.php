<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class VarDateTimeType
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\VarDateTimeType
 */
class VarDateTimeType extends DateTimeType
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTime)
        {
            return $value;
        }

        $val = date_create($value);

        if (!$val)
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}