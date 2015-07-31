<?php namespace Chaos\Common;

use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class AbstractDoctrineRepository
 * @author ntd1712
 */
abstract class AbstractDoctrineRepository extends EntityRepository implements IDoctrineRepository
{
    use BaseDoctrineRepositoryTrait, Traits\ConfigAwareTrait, Traits\ContainerAwareTrait;

    /** {@inheritdoc} @param bool $fetchJoinCollection */
    public function paginate($criteria = [], array $paging = [], $fetchJoinCollection = true)
    {
        $query = $this->getQueryBuilder($criteria);

        if (null === $query->getFirstResult())
        {
            $query->setFirstResult(@$paging['CurrentPageStart'] ?: 0);
        }

        if (null === $query->getMaxResults())
        {
            $query->setMaxResults(@$paging['ItemCountPerPage'] ?: CHAOS_SQL_BATCH_SIZE);
        }

        return new Paginator($query, $fetchJoinCollection);
    }

    /** {@inheritdoc} @param int $hydrationMode */
    public function readAll($criteria = [], $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        $result = $this->getQueryBuilder($criteria)->getQuery()->getResult($hydrationMode);
        return new \ArrayIterator($result);
    }

    /** {@inheritdoc} @param int $hydrationMode */
    public function read($criteria, $hydrationMode = null)
    {
        $result = $this->getQueryBuilder($criteria)->setMaxResults(1)->getQuery()->getOneOrNullResult($hydrationMode);
        return $result;
    }

    /** {@inheritdoc} */
    public function create($entity)
    {
        return $this->update($entity, null, true);
    }

    /** {@inheritdoc} */
    public function update($entity, $where = null, $isNew = false)
    {
        if (!is_array($entity))
        {
            $entity = [$entity];
        }

        $i = 0;

        foreach ($entity as $v)
        {
            $isNew ? $this->_em->persist($v) : $v = $this->_em->merge($v);

            if (0 === (++$i % CHAOS_SQL_BATCH_SIZE))
            {
                $this->_em->flush();
            }
        }

        if (0 !== $i)
        {
            $this->_em->flush();
        }

        return $i;
    }

    /** {@inheritdoc} */
    public function delete($criteria, $autoFlush = true)
    {
        $entities = is_object($criteria) ? [$criteria] : $this->getQueryBuilder($criteria)->getQuery()->getResult();

        if (!empty($entities))
        {
            $i = 0;

            foreach ($entities as $v)
            {
                if ($this->_em->contains($v))
                {
                    $this->_em->remove($v);

                    if ($autoFlush && 0 === (++$i % CHAOS_SQL_BATCH_SIZE))
                    {
                        $this->_em->flush();
                    }
                }
            }

            if ($autoFlush && 0 !== $i)
            {
                $this->_em->flush();
            }

            return $i;
        }

        return 0;
    }

    /** {@inheritdoc} */
    public function exist($criteria, $fieldName = null)
    {
        if (is_scalar($criteria))
        {
            if (isset($fieldName))
            {
                $criteria = [$fieldName => $criteria];
            }
            elseif (!empty($this->_class->identifier))
            {
                $instance = Criteria::create();

                foreach ($this->_class->identifier as $v)
                {
                    $instance->orWhere($instance->expr()->eq($v, $criteria));
                }

                $criteria = $instance;
            }
        }

        if ($criteria instanceof Criteria)
        {
            return 0 !== count($this->matching($criteria));
        }

        return null !== $this->findOneBy($criteria);
    }

    /** {@inheritdoc} */
    public function refine()
    {
        $this->_em->clear();
        return $this;
    }

    /** {@inheritdoc} */
    public function __get($key)
    {
        switch ($key)
        {
            // general
            case 'className':
                return $this->_class->reflClass->getShortName();
            case 'entityName':
                return $this->_entityName;
            case 'entity':
                return new $this->_entityName;
            case 'fields':
                return $this->_class->fieldMappings;
            case 'pk': // primary key
                return $this->_class->identifier;
            // specific
            case 'criteria':
                return Criteria::create();
            case 'entityManager':
                return $this->_em;
            case 'metadata':
                return $this->_class;
            default:
                throw new Exceptions\BadMethodCallException('Invalid magic property on repository');
        }
    }
}