<?php namespace Chaos\Common\Enums;

/**
 * Class PredicateType
 * @author ntd1712
 */
class PredicateType extends Enum
{
    const BETWEEN = 'between';
    const NOT_BETWEEN = 'notBetween';

    const EQUAL_TO = 'equalTo';
    const NOT_EQUAL_TO = 'notEqualTo';
    const LESS_THAN = 'lessThan';
    const GREATER_THAN = 'greaterThan';
    const LESS_THAN_OR_EQUAL_TO = 'lessThanOrEqualTo';
    const GREATER_THAN_OR_EQUAL_TO = 'greaterThanOrEqualTo';

    const EQ = '=';
    const NEQ = '<>';
    const LT = '<';
    const LTE = '<=';
    const GT  = '>';
    const GTE = '>=';

    const EXPR = 'expr';
    const EXPRESSION = 'expression';

    const IN = 'in';
    const NIN = 'nin';
    const NOT_IN = 'notIn';

    const IS_NOT_NULL = 'isNotNull';
    const IS_NULL = 'isNull';

    const LIKE = 'like';
    const NOT_LIKE = 'notLike';

    const NEST = 'nest';
    const UNNEST = 'unnest';

    const ASC = 'ASC';
    const DESC = 'DESC';
    const NULLS_FIRST = 'NULLS FIRST';
    const NULLS_LAST = 'NULLS LAST';

    /** @var array */
    protected static $map = [
        self::BETWEEN => true,
        self::NOT_BETWEEN => true,
        self::EQUAL_TO => true,
        self::NOT_EQUAL_TO => true,
        self::LESS_THAN => true,
        self::GREATER_THAN => true,
        self::LESS_THAN_OR_EQUAL_TO => true,
        self::GREATER_THAN_OR_EQUAL_TO => true,
        self::EQ => true,
        self::NEQ => true,
        self::LT => true,
        self::LTE => true,
        self::GT => true,
        self::GTE => true,
        self::EXPR => true,
        self::EXPRESSION => true,
        self::IN => true,
        self::NIN => true,
        self::NOT_IN => true,
        self::IS_NOT_NULL => true,
        self::IS_NULL => true,
        self::LIKE => true,
        self::NOT_LIKE => true,
        self::NEST => true,
        self::UNNEST => true,
        self::ASC => true,
        self::DESC => true,
        self::NULLS_FIRST => true,
        self::NULLS_LAST => true
    ];
}