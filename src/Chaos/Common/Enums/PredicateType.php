<?php namespace Chaos\Common\Enums;

/**
 * Class PredicateType
 * @author ntd1712
 */
class PredicateType
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

    /** Private constructor */
    final private function __construct() {}
}