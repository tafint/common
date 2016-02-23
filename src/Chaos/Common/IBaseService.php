<?php namespace Chaos\Common;

/**
 * Interface IBaseService
 * @author ntd1712
 *
 * @property-read string $className The short class name of the entity
 * @property-read string $entityName The qualified class name of the entity
 * @property-read IBaseEntity $entity The entity instance
 * @property-read array $fields The field mappings of the entity
 * @property-read array $pk The field names that are part of the identifier/primary key of the entity
 *
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance
 * @property-read \Doctrine\ORM\Query\Expr $expression The <tt>Expr</tt> instance
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $metadata The <tt>ClassMetadata</tt> instance
 */
interface IBaseService
{
    /** The events being trigger */
    const ON_AFTER_READ_ALL = 'onAfterReadAll',
          ON_AFTER_READ = 'onAfterRead',
          ON_EXCHANGE_ARRAY = 'onExchangeArray',
          ON_VALIDATE = 'onValidate',
          ON_BEFORE_SAVE = 'onBeforeSave',
          ON_AFTER_SAVE = 'onAfterSave',
          ON_BEFORE_DELETE = 'onBeforeDelete',
          ON_AFTER_DELETE = 'onAfterDelete';
    /**
     * The default "readAll" method, you can override this in derived class
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The query criteria
     * @param   boolean|array $paging The paging criteria, default boolean false
     * @return  array
     */
    function readAll($criteria = [], $paging = false);
    /**
     * The default "read" method, you can override this in derived class
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The query criteria
     * @return  array
     * @throws  Exceptions\ServiceException
     */
    function read($criteria);
    /**
     * The default "create" method, you can override this in derived class
     *
     * @param   array $post The _POST variable
     * @return  array
     * @throws  Exceptions\ServiceException
     * @throws  Exceptions\ValidateException
     */
    function create(array $post = []);
    /**
     * The default "update" method, you can override this in derived class
     *
     * @param   array $post The _PUT variable
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The update criteria
     * @return  array
     * @throws  Exceptions\ServiceException
     * @throws  Exceptions\ValidateException
     */
    function update(array $post = [], $criteria = null);
    /**
     * The default "delete" method, you can override this in derived class
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The delete criteria
     * @return  array
     * @throws  Exceptions\ServiceException
     */
    function delete($criteria);
}