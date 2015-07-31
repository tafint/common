<?php namespace Chaos\Common\Types\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Chaos\Common\Types\Type as DataType;

/**
 * Class MediumIntType
 * @author ntd1712
 */
class MediumIntType extends Type
{
    /** {@inheritdoc} */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_object($value))
        {
            throw ConversionException::conversionFailed($value, $this);
        }

        return null === $value ? null : (int)$value;
    }

    /** {@inheritdoc} */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        switch ($platform->getName())
        {
            case 'mysql':
                return 'MEDIUMINT' . (isset($fieldDeclaration['unsigned']) && $fieldDeclaration['unsigned'] ? ' UNSIGNED' : '');
            default:
                return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
        }
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return DataType::MEDIUMINT_TYPE;
    }

    /** {@inheritdoc} */
    public function getBindingType()
    {
        return \PDO::PARAM_INT;
    }
}