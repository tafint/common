<?php namespace Chaos\Common;

/**
 * Interface IBaseRepository
 * @author ntd1712
 *
 * @property-read string $className The entity short name
 * @property-read string $entityName The entity name
 * @property-read IBaseEntity $entity The <tt>entity</tt> instance
 * @property-read array $fields The field mappings of the <tt>entity</tt>
 * @property-read array $pk The field names that are part of the identifier/primary key of the <tt>entity</tt>
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance
 * @property-read \Doctrine\ORM\Query\Expr $expression The <tt>Expr</tt> instance
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $metadata The <tt>ClassMetadata</tt> instance
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
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria The query criteria
     * @param   array $paging The paging criteria
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     */
    function paginate($criteria = [], array $paging = []);
    /**
     * The default "readAll" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria The query criteria
     * @return  \ArrayIterator
     */
    function readAll($criteria = []);
    /**
     * The default "read" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria The query criteria
     * @return  IBaseEntity
     */
    function read($criteria);
    /**
     * The default "create" method, you can override this in derived class
     *
     * @param   array|IBaseEntity $entity Either an associative array of key values or an object
     * @return  int|string Either the affected rows or the last inserted Id
     */
    function create($entity);
    /**
     * The default "update" method, you can override this in derived class
     *
     * @param   array|IBaseEntity $entity Either an associative array of key values or an object
     * @param   array|string|\Closure $where The update condition
     * @return  int The affected rows
     */
    function update($entity, $where = null);
    /**
     * The default "delete" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder|IBaseEntity $criteria The delete criteria
     * @return  int The affected rows
     */
    function delete($criteria);
    /**
     * The default "exist" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|mixed $criteria Either a query criteria or a field value
     * @param   string $fieldName The field name; defaults to "Id"
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