<?php namespace Chaos\Common\Types;

use Chaos\Common\Exceptions\ConversionException;

/**
 * Class Type
 * @author ntd1712
 *
 * @see Doctrine\DBAL\Types\Type
 */
abstract class Type
{
    /** Doctrine */
    const ARRAY_TYPE = 'array';
    const BIGINT_TYPE = 'bigint';
    const BINARY_TYPE = 'binary';
    const BLOB_TYPE = 'blob';
    const BOOLEAN_TYPE = 'boolean';
    const DATETIME_TYPE = 'datetime';
    const DATETIMETZ_TYPE = 'datetimetz';
    const DATE_TYPE = 'date';
    const DECIMAL_TYPE = 'decimal';
    const FLOAT_TYPE = 'float';
    const GUID_TYPE = 'guid';
    const INTEGER_TYPE = 'integer';
    const JSON_ARRAY_TYPE = 'json_array';
    const OBJECT_TYPE = 'object';
    const SIMPLE_ARRAY_TYPE = 'simple_array';
    const SMALLINT_TYPE = 'smallint';
    const STRING_TYPE = 'string';
    const TEXT_TYPE = 'text';
    const TIME_TYPE = 'time';
    const VARDATETIME_TYPE = 'vardatetime';
    /** Custom */
    const ENUM_TYPE = 'enum';
    const MEDIUMINT_TYPE = 'mediumint';
    const NULL_TYPE = 'null';
    const TIMESTAMP_TYPE = 'timestamp';
    const TINYINT_TYPE = 'tinyint';
    /** Misc. */
    const BOOL_TYPE = 'bool';
    const DOUBLE_TYPE = 'double';
    const INT_TYPE = 'int';
    const MIXED_TYPE = 'mixed';
    const RESOURCE_TYPE = 'resource';
    const UUID_TYPE = 'uuid';
    const UNKNOWN_TYPE = 'unknown';

    /** @var array Map of supported types */
    private static $typesMap = [
        self::ARRAY_TYPE => 'Chaos\Common\Types\ArrayType',
        self::BIGINT_TYPE => 'Chaos\Common\Types\BigIntType',
        self::BINARY_TYPE => 'Chaos\Common\Types\BinaryType',
        self::BLOB_TYPE => 'Chaos\Common\Types\BlobType',
        self::BOOLEAN_TYPE => 'Chaos\Common\Types\BooleanType',
        self::BOOL_TYPE => 'Chaos\Common\Types\BooleanType',
        self::DATETIME_TYPE => 'Chaos\Common\Types\DateTimeType',
        self::DATETIMETZ_TYPE => 'Chaos\Common\Types\DateTimeTzType',
        self::DATE_TYPE => 'Chaos\Common\Types\DateType',
        self::DECIMAL_TYPE => 'Chaos\Common\Types\DecimalType',
        self::DOUBLE_TYPE => 'Chaos\Common\Types\FloatType',
        self::ENUM_TYPE => 'Chaos\Common\Types\EnumType',
        self::FLOAT_TYPE => 'Chaos\Common\Types\FloatType',
        self::GUID_TYPE => 'Chaos\Common\Types\GuidType',
        self::INTEGER_TYPE => 'Chaos\Common\Types\IntegerType',
        self::INT_TYPE => 'Chaos\Common\Types\IntegerType',
        self::JSON_ARRAY_TYPE => 'Chaos\Common\Types\JsonArrayType',
        self::MEDIUMINT_TYPE => 'Chaos\Common\Types\MediumIntType',
        self::MIXED_TYPE => 'Chaos\Common\Types\MixedType',
        self::NULL_TYPE => 'Chaos\Common\Types\NullType',
        self::OBJECT_TYPE => 'Chaos\Common\Types\ObjectType',
        self::RESOURCE_TYPE => 'Chaos\Common\Types\ResourceType',
        self::SIMPLE_ARRAY_TYPE => 'Chaos\Common\Types\SimpleArrayType',
        self::SMALLINT_TYPE => 'Chaos\Common\Types\SmallIntType',
        self::STRING_TYPE => 'Chaos\Common\Types\StringType',
        self::TEXT_TYPE => 'Chaos\Common\Types\TextType',
        self::TIMESTAMP_TYPE => 'Chaos\Common\Types\TimestampType',
        self::TIME_TYPE => 'Chaos\Common\Types\TimeType',
        self::TINYINT_TYPE => 'Chaos\Common\Types\TinyIntType',
        self::UNKNOWN_TYPE => 'Chaos\Common\Types\UnknownType',
        self::UUID_TYPE => 'Chaos\Common\Types\UuidType',
        self::VARDATETIME_TYPE => 'Chaos\Common\Types\VarDateTimeType',
    ];
    /** @var array Map of already instantiated type objects */
    private static $typeObjects = [];

    /** Private constructor: to prevents instantiation and forces use of the factory method */
    final private function __construct() {}

    /** @return string */
    public function __toString()
    {
        return str_replace('Type', '', shorten(get_class($this)));
    }

    /**
     * Convert a value from its database representation to its PHP representation of this type
     *
     * @param   mixed $value Value to convert
     * @return  mixed The PHP representation of the value
     */
    public function convertToPHPValue($value)
    {
        return $value;
    }

    /**
     * Factory method to create type instances. Type instances are implemented as flyweights
     *
     * @param   string $name Name of the type
     * @return  Type
     * @throws  \Chaos\Common\Exceptions\ConversionException
     */
    public static function getType($name)
    {
        if (!isset(self::$typeObjects[$name]))
        {
            if (!isset(self::$typesMap[$name]))
            {
                throw ConversionException::unknownColumnType($name);
            }

            self::$typeObjects[$name] = new self::$typesMap[$name];
        }

        return self::$typeObjects[$name];
    }

    /**
     * Check if exists support for a type
     *
     * @param   string $name Name of the type
     * @return  boolean TRUE if type is supported, FALSE otherwise
     */
    public static function hasType($name)
    {
        return isset(self::$typesMap[$name]);
    }

    /**
     * Get the types array map which holds all registered types and the corresponding type class
     *
     * @return  array
     */
    public static function getTypesMap()
    {
        return self::$typesMap;
    }
}