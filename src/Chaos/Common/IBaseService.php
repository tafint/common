<?php namespace Chaos\Common;

/**
 * Interface IBaseService
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
 * @method IBaseRepository|\Doctrine\ORM\EntityRepository getRepository(string $name = null)
 *   Get the <tt>repository</tt> instance
 * @method IBaseService getService(string $name = null) Get the <tt>service</tt> instance
 * @method IBaseEntity getUser(string $token = null) Get the <tt>user</tt> instance
 *
 * @method \Zend\Db\Sql\Predicate\Predicate prepareFilterParams($binds = [], \Zend\Db\Sql\Predicate\PredicateInterface $predicate = null)
 *   Prepare filter parameters
 * @method array prepareOrderParams(array $binds = []) Prepare order parameters
 * @method array preparePagerParams(array $binds = []) Prepare pager parameters
 * @method string filter(string $value, bool $checkDate = false) Return the string $value, converting characters to
 *   their corresponding HTML entity equivalents where they exist
 */
interface IBaseService
{
    /** The events being trigger */
    const ON_EXCHANGE_ARRAY = 'onExchangeArray',
          ON_VALIDATE = 'onValidate',
          ON_BEFORE_SAVE = 'onBeforeSave',
          ON_AFTER_SAVE = 'onAfterSave',
          ON_BEFORE_DELETE = 'onBeforeDelete',
          ON_AFTER_DELETE = 'onAfterDelete';
    /**
     * The default "readAll" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder $criteria Query criteria
     * @param   bool|array $paging Paging criteria, default boolean false
     * @return  array
     */
    function readAll($criteria = [], $paging = false);
    /**
     * The default "read" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder|mixed $criteria Query criteria
     * @return  array
     * @throws  Exceptions\InvalidArgumentException
     * @throws  Exceptions\ServiceException
     */
    function read($criteria);
    /**
     * The default "create" method, you can override this in derived class
     *
     * @param   array $post POST variable
     * @return  array
     * @throws  Exceptions\ServiceException
     * @throws  Exceptions\ValidateException
     */
    function create(array $post = []);
    /**
     * The default "update" method, you can override this in derived class
     *
     * @param   array $post PUT variable
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder|mixed $criteria Update criteria
     * @return  array
     * @throws  Exceptions\InvalidArgumentException
     * @throws  Exceptions\ServiceException
     * @throws  Exceptions\ValidateException
     */
    function update(array $post = [], $criteria = null);
    /**
     * The default "delete" method, you can override this in derived class
     *
     * @param   array|\Doctrine\Common\Collections\Criteria|\Doctrine\ORM\QueryBuilder|mixed $criteria Delete criteria
     * @return  array
     * @throws  Exceptions\ServiceException
     */
    function delete($criteria);
}