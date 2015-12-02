<?php namespace Chaos\Common;

/**
 * Interface IDoctrineRepository
 * @author ntd1712
 *
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance
 * @property-read \Doctrine\ORM\Query\Expr $expression The <tt>Expr</tt> instance
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $metadata The <tt>ClassMetadata</tt> instance
 */
interface IDoctrineRepository extends IBaseRepository {}