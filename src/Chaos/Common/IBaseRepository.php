<?php namespace Chaos\Common;

/**
 * Interface IBaseRepository
 * @author ntd1712
 *
 * @property-read string $className Entity short name
 * @property-read string $entityName Entity name
 * @property-read IBaseEntity $entity <tt>Entity</tt> instance
 * @property-read array $fields Field mappings
 * @property-read array $pk Primary key(s)
 * @property-read \Doctrine\Common\Collections\Criteria $criteria <tt>Criteria</tt> instance
 * @property-read \Doctrine\ORM\Query\Expr $expression <tt>Expr</tt> instance
 * @property-read \Doctrine\ORM\EntityManager $entityManager <tt>EntityManager</tt> instance
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $metadata <tt>ClassMetadata</tt> instance
 *
 * @method IBaseRepository beginTransaction() Start a transaction by suspending auto-commit mode
 * @method IBaseRepository commit() Commit the current transaction
 * @method IBaseRepository rollBack() Cancel any database changes done during the current transaction
 */
interface IBaseRepository
{
    /**
     * The default "paginate" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria Query criteria
     * @param   array $paging Paging criteria
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     */
    function paginate($criteria = [], array $paging = []);
    /**
     * The default "readAll" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria Query criteria
     * @return  \ArrayIterator
     */
    function readAll($criteria = []);
    /**
     * The default "read" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria Query criteria
     * @return  IBaseEntity
     */
    function read($criteria);
    /**
     * The default "create" method, you can override this in derived class
     *
     * @param   array|IBaseEntity $entity Either an associative array of key values or an object
     * @return  int Affected rows
     */
    function create($entity);
    /**
     * The default "update" method, you can override this in derived class
     *
     * @param   array|IBaseEntity $entity Either an associative array of key values or an object
     * @param   array|string|\Closure $where Update condition
     * @param   bool $isNew Flag indicates we are creating or updating a record
     * @return  int Affected rows
     */
    function update($entity, $where = null, $isNew = false);
    /**
     * The default "delete" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder|IBaseEntity $criteria Delete criteria
     * @return  int Affected rows
     */
    function delete($criteria);
    /**
     * The default "exist" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|mixed $criteria Either a query criteria or column value
     * @param   string $fieldName Field name; defaults to "Id"
     * @return  bool
     */
    function exist($criteria, $fieldName = null);
    /**
     * The default "refine" method, you can override this in derived class
     *
     * @return  $this
     */
    function refine();
}