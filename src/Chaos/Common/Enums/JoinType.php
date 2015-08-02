<?php namespace Chaos\Common\Enums;

/**
 * Class JoinType
 * @author ntd1712
 */
class JoinType extends Enum
{
    const JOIN = 'join';
    const INNER_JOIN = 'innerJoin';

    const LEFT_JOIN = 'leftJoin';
    const LEFT_OUTER_JOIN = 'leftOuterJoin';

    const RIGHT_JOIN = 'rightJoin';
    const RIGHT_OUTER_JOIN = 'rightOuterJoin';

    const FULL_JOIN = 'fullJoin';
    const FULL_OUTER_JOIN = 'fullOuterJoin';

    /** @var array */
    protected static $map = [
        self::JOIN => true,
        self::INNER_JOIN => true,
        self::LEFT_JOIN => true,
        self::LEFT_OUTER_JOIN => true,
        self::RIGHT_JOIN => true,
        self::RIGHT_OUTER_JOIN => true,
        self::FULL_JOIN => true,
        self::FULL_OUTER_JOIN => true
    ];
}