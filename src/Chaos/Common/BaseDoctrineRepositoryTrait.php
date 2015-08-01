<?php namespace Chaos\Common;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\PredicateInterface;

/**
 * Class BaseDoctrineRepositoryTrait
 * @author ntd1712
 */
trait BaseDoctrineRepositoryTrait
{
    /** {@inheritdoc} @return $this */
    public function beginTransaction()
    {
        $this->_em->getConnection()->beginTransaction();
        return $this;
    }

    /** {@inheritdoc} @return $this */
    public function commit()
    {
        if ($this->_em->getConnection()->isTransactionActive() && !$this->_em->getConnection()->isRollbackOnly())
        {
            $this->_em->getConnection()->commit();
        }

        return $this;
    }

    /** {@inheritdoc} @return $this */
    public function rollBack()
    {
        if ($this->_em->getConnection()->isTransactionActive())
        {
            $this->_em->getConnection()->rollBack();
        }

        return $this;
    }

    /**
     * Get QueryBuilder instance
     *
     * @param   array|Criteria|\Doctrine\ORM\QueryBuilder $criteria Query criteria
     * @param   QueryBuilder $queryBuilder
     * @return  QueryBuilder
     * @throws  Exceptions\InvalidArgumentException
     */
    protected function getQueryBuilder($criteria, QueryBuilder $queryBuilder = null)
    {
        // do some checks
        $rootAlias = $this->_class->reflClass->getShortName();

        if (null === $queryBuilder)
        {   /** @see \Doctrine\ORM\EntityRepository::createQueryBuilder */
            $queryBuilder = $this->createQueryBuilder($rootAlias);
        }

        if ($criteria instanceof Criteria)
        {
            return $queryBuilder->addCriteria($criteria);
        }
        elseif (empty($criteria) || !is_array($criteria))
        {
            return $queryBuilder;
        }

        // switch...
        foreach ($criteria as $k => $v)
        {
            if (empty($v))
            {
                continue;
            }

            $aliases = $queryBuilder->getAllAliases();

            switch ($k)
            {
                case Select::TABLE:
                case 'from':
                    // e.g. ['from' => $this->getRepository('User')]
                    //      ['from' => 'User u INDEX BY u.Id, Role r, Permission']
                    //      ['from' => ['from' => 'User', 'alias' => 'u', 'indexBy' => 'u.Id']]
                    //      ['from' => [
                    //          ['from' => 'User', 'alias' => 'u', 'indexBy' => 'u.Id'],
                    //          ['from' => 'Role', 'alias' => 'r'],
                    //          ['from' => $this->getRepository('Permission')]
                    //      ]]
                    if ($v instanceof IBaseRepository)
                    {
                        $v = $v->getClassName();
                    }

                    if (is_string($v))
                    {
                        $matches = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                        $v = [];

                        foreach ($matches as $m)
                        {
                            $parts = preg_split(CHAOS_REPLACE_SPACE_SEPARATOR, $m, -1, PREG_SPLIT_NO_EMPTY);
                            $v[] = ['from' => $parts[0], 'alias' => @$parts[1], 'indexBy' => @$parts[4]];
                        }
                    }
                    elseif (!is_array($v))
                    {
                        throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    if (isset($v['from'])) // make sure we have a multidimensional array passed
                    {
                        $v = [$v];
                    }

                    foreach ($v as $from)
                    {
                        if (!is_array($from) || empty($from['from']))
                        {
                            throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format" .
                                ' and its required key "from"');
                        }

                        if ($from['from'] instanceof IBaseRepository)
                        {
                            $from['from'] = $from['from']->getClassName();
                        }

                        if (false === strpos($from['from'], '\\')) // use default namespace if any
                        {
                            $from['from'] = $this->_class->namespace . '\\' . $from['from'];
                        }

                        if (in_array($from['from'], $queryBuilder->getRootEntities()))
                        {
                            continue;
                        }

                        if (!isset($from['alias']))
                        {
                            $from['alias'] = shorten($from['from']);
                        }

                        if (!isset($from['indexBy']))
                        {
                            $from['indexBy'] = null;
                        }

                        $queryBuilder->from(trim($from['from']), trim($from['alias']), $from['indexBy']);
                    }
                    break;
                case Select::COLUMNS:
                case Select::SELECT:
                    // e.g. ['select' => '*']
                    //      ['select' => $this->getRepository('User')]
                    //      ['select' => 'User, Role']
                    //      ['select' => ['User', 'Role']
                    //      ['select' => ['User.Id', 'Role.Id']
                    //      ['select' => [
                    //          $this->getRepository('User'),
                    //          $this->getRepository('Role')
                    //      ]
                    if ($v instanceof IBaseRepository)
                    {
                        $v = $v->className;
                    }

                    if (is_string($v))
                    {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    }
                    elseif (!is_array($v))
                    {
                        throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $select)
                    {
                        if (empty($select) || Select::SQL_STAR === $select)
                        {
                            continue;
                        }

                        if ($select instanceof IBaseRepository)
                        {
                            $select = $select->className;
                        }

                        $queryBuilder->addSelect($select);
                    }

                    // check if an array has duplicates
                    $dqlPart = array_unique($queryBuilder->getDQLPart('select'));
                    $queryBuilder->resetDQLPart('select');

                    foreach ($dqlPart as $select)
                    {
                        $queryBuilder->add('select', $select, true);
                    }
                    break;
                case 'distinct':
                    // e.g. ['distinct' => true]
                    $queryBuilder->distinct();
                    break;
                case Select::QUANTIFIER:
                    // e.g. ['quantifier' => 'distinct']
                    $queryBuilder->distinct(Select::QUANTIFIER_DISTINCT === strtoupper($v));
                    break;
                case Select::JOINS:
                case 'join':
                    // e.g. ['joins' => ['join' => $this->getRepository('UserRole')]]
                    //      ['joins' => ['join' => $this->getRepository('User'), 'condition' => '%3$s = %2$s.%3$s']]
                    if (!is_array($v))
                    {
                        throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    if (isset($v['join']))
                    {
                        $v = [$v];
                    }

                    foreach ($v as $join)
                    {
                        if (!is_array($join) || empty($join['join']))
                        {
                            throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format" .
                                ' and its required key "join"');
                        }

                        if ($join['join'] instanceof IBaseRepository)
                        {
                            $join['join'] = $join['join']->getClassName();
                        }

                        if (!isset($join['alias']))
                        {
                            $join['alias'] = shorten($join['join']);
                        }

                        if (!isset($join['conditionType']) ||
                            !in_array(strtoupper($join['conditionType']), [Join::ON, Join::WITH]))
                        {
                            $join['conditionType'] = Join::WITH;
                        }

                        array_push($aliases, $join['alias']);

                        if (false !== ($condition = @vsprintf( // guess JOIN condition
                            isset($join['condition']) ? $join['condition'] : '%2$s = %1$s.%2$s', $aliases)))
                        {
                            $join['condition'] = $condition;
                        }
                        else
                        {
                            $join['condition'] = null;
                        }

                        if (!isset($join['indexBy']))
                        {
                            $join['indexBy'] = null;
                        }

                        if (!isset($join['type']) || Join::LEFT_JOIN !== strtoupper($join['type']))
                        {
                            $join['type'] = Join::INNER_JOIN;
                        }

                        /* @see Doctrine\ORM\QueryBuilder::innerJoin
                         * @see Doctrine\ORM\QueryBuilder::leftJoin */
                        call_user_func([$queryBuilder, strtolower($join['type']) . 'Join'],
                            $join['join'], $join['alias'], $join['conditionType'], $join['condition'], $join['indexBy']);
                    }
                    break;
                case Select::COMBINE:
                case 'set':
                    throw new Exceptions\InvalidArgumentException('Not implemented yet');
                case Select::WHERE:
                case Select::HAVING:
                    // e.g. ['where' => 'Id = 1']
                    //      ['where' => ['Id' => 1]]
                    //      ['where' => ['Id' => [1]]]
                    //      ['where' => \Zend\Db\Sql\Predicate\PredicateSet]
                    if ($v instanceof PredicateInterface)
                    {
                        $predicateSet = $v->getPredicates();

                        foreach ($predicateSet as $value)
                        {
                            $predicates = $value[1]->getPredicates();

                            if (empty($predicates))
                            {
                                continue;
                            }

                            $predicate = $predicates[0][1];
                            $name = shorten(get_class($predicate));
                            $expr = $queryBuilder->expr();

                            if (method_exists($predicate, 'getIdentifier'))
                            {
                                if (false === strpos($predicate->getIdentifier(), '.'))
                                {
                                    $predicate->setIdentifier($rootAlias . '.' . $predicate->getIdentifier());
                                }
                            }

                            switch ($name)
                            {
                                case 'Between':
                                case 'NotBetween':
                                    $expr = sprintf($predicate->getSpecification(), $predicate->getIdentifier(),
                                        $predicate->getMinValue(), $predicate->getMaxValue());
                                    break;
                                case 'Expression': // @TODO: Expression, Predicate NEST/UNNEST
                                case 'Predicate':
                                    throw new Exceptions\InvalidArgumentException($name . ' is not implemented yet');
                                case 'In':
                                case 'NotIn':
                                    /* @see Doctrine\ORM\Query\Expr::in
                                     * @see Doctrine\ORM\Query\Expr::notIn */
                                    $expr = $expr->{lcfirst($name)}($predicate->getIdentifier(), $predicate->getValueSet());
                                    break;
                                case 'IsNotNull':
                                case 'IsNull':
                                    /* @see Doctrine\ORM\Query\Expr::isNull
                                     * @see Doctrine\ORM\Query\Expr::isNotNull */
                                    $expr = $expr->{lcfirst($name)}($predicate->getIdentifier());
                                    break;
                                case 'Like':
                                case 'NotLike':
                                    /* @see Doctrine\ORM\Query\Expr::like
                                     * @see Doctrine\ORM\Query\Expr::notLike */
                                    $expr = $expr->{lcfirst($name)}($predicate->getIdentifier(), $predicate->getLike());
                                    break;
                                case 'Literal':
                                    $expr = $expr->literal($predicate->getLiteral());
                                    break;
                                default: // Operator
                                    if (PredicateInterface::TYPE_IDENTIFIER === $predicate->getLeftType())
                                    {
                                        $left = $predicate->getLeft();
                                        $right = $predicate->getRight();
                                    }
                                    else
                                    {
                                        $left = $predicate->getRight();
                                        $right = $predicate->getLeft();
                                    }

                                    if (false === strpos($left, '.'))
                                    {
                                        $left = $rootAlias . '.' . $left;
                                    }

                                    $expr = new Comparison($left, $predicate->getOperator(), $right);
                            }

                            /* @see Doctrine\ORM\QueryBuilder::andWhere
                             * @see Doctrine\ORM\QueryBuilder::andHaving */
                            $queryBuilder->{strtolower($value[0]) . ucfirst($k)}($expr);
                        }
                    }
                    elseif (is_array($v))
                    {
                        $count = 0;

                        foreach ($v as $key => $value)
                        {
                            if (false === strpos($key, '.'))
                            {
                                $key = $rootAlias . '.' . trim($key);
                            }

                            $queryBuilder->{'and' . ucfirst($k)}(is_array($value) ?
                                $queryBuilder->expr()->in($key, '?' . $count) :
                                $queryBuilder->expr()->eq($key, '?' . $count)
                            )->setParameter($count++, $value);
                        }
                    }
                    else
                    {
                        /* @see Doctrine\ORM\QueryBuilder::where
                         * @see Doctrine\ORM\QueryBuilder::having */
                        $queryBuilder->$k($v);
                    }
                    break;
                case Select::GROUP:
                case 'groupBy':
                    // e.g. ['group' => 'Id, Name']
                    //      ['group' => ['Id', 'Name']]
                    if (is_string($v))
                    {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    }
                    elseif (!is_array($v))
                    {
                        throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $groupBy)
                    {
                        if (is_empty($groupBy))
                        {
                            continue;
                        }

                        if (false === strpos($groupBy, '.'))
                        {
                            $groupBy = $rootAlias . '.' . $groupBy;
                        }

                        $queryBuilder->addGroupBy($groupBy);
                    }
                    break;
                case Select::ORDER:
                case 'orderBy':
                    // e.g. ['order' => 'Id DESC, Name']
                    //      ['order' => ['Id DESC', 'Name ASC NULLS FIRST']]
                    //      ['order' => ['Id' => 'DESC', 'Name ASC NULLS FIRST']]
                    if (is_string($v))
                    {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    }
                    elseif (!is_array($v))
                    {
                        throw new Exceptions\InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $key => $value)
                    {
                        if (is_string($key))
                        {
                            if (is_empty($key))
                            {
                                continue;
                            }

                            preg_match(CHAOS_MATCH_ASC_DESC, $key . ' ' . $value, $matches);
                        }
                        else
                        {
                            if (is_empty($value))
                            {
                                continue;
                            }

                            preg_match(CHAOS_MATCH_ASC_DESC, $value, $matches);
                        }

                        if (!empty($matches[1]))
                        {
                            $order = Select::ORDER_ASCENDING;

                            if (isset($matches[2]) && Select::ORDER_DESCENDING === strtoupper($matches[2]))
                            {
                                $order = Select::ORDER_DESCENDING;
                            }

                            if (!is_empty($matches[3]))
                            {
                                $order .= ' ' . trim($matches[3]);
                            }

                            if (false === strpos($matches[1], '.'))
                            {
                                $matches[1] = $rootAlias . '.' . $matches[1];
                            }

                            $queryBuilder->addOrderBy($matches[1], $order);
                        }
                    }
                    break;
                case Select::LIMIT:
                    $queryBuilder->setMaxResults((int)$v);
                    break;
                case Select::OFFSET:
                    $queryBuilder->setFirstResult((int)$v);
                    break;
                default:
            }
        }

        // bye!
        return $queryBuilder;
    }

    /**
     * Get SQL string for statement
     *
     * @param  QueryBuilder $queryBuilder
     * @return string
     */
    protected function getSqlString(QueryBuilder $queryBuilder)
    {
        return $queryBuilder->getQuery()->getSQL();
    }
}