<?php namespace Chaos\Common;

/**
 * Class AbstractBaseService
 * @author ntd1712
 */
abstract class AbstractBaseService implements IBaseService
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait, Traits\ServiceAwareTrait, Traits\RepositoryAwareTrait,
        BaseServiceTrait;

    /** {@inheritdoc} */
    public function readAll($criteria = [], $paging = false)
    {
        // get items
        if (false !== $paging)
        {
            $entities = $this->getRepository()->paginate($criteria, $paging);
        }
        else
        {
            $entities = $this->getRepository()->readAll($criteria);
        }

        // prepare data for output
        $result = ['data' => [], 'total' => count($entities), 'success' => true];

        if (0 !== $result['total'])
        {
            // fire events if any
            $this->trigger(static::ON_AFTER_READ_ALL, [CHAOS_READ_EVENT_ARGS, func_get_args(), $entities]);
            // copy the iterator into an array
            $result['data'] = $entities instanceof \Traversable ? iterator_to_array($entities) : $entities;
        }

        // bye!
        return $result;
    }

    /** {@inheritdoc} */
    public function read($criteria)
    {
        // get item
        if (is_scalar($criteria))
        {
            if (is_numeric($criteria))
            {
                $criteria = (int)$criteria;

                if (1 > $criteria)
                {
                    throw new Exceptions\ServiceException('Your request is invalid');
                }
            }
            else
            {
                $criteria = $this->filter($criteria);

                if (empty($criteria))
                {
                    throw new Exceptions\ServiceException('Your request is invalid');
                }
            }

            $entity = $this->getRepository()->find($criteria);
        }
        else
        {
            if (empty($criteria))
            {
                throw new Exceptions\InvalidArgumentException(__METHOD__ . ' expects "$criteria" in array format');
            }

            $entity = $this->getRepository()->read($criteria);
        }

        if (null === $entity)
        {
            throw new Exceptions\ServiceException('Your request is invalid');
        }

        // fire events if any
        $this->trigger(static::ON_AFTER_READ, [CHAOS_READ_EVENT_ARGS, $criteria, $entity]);
        // prepare data for output
        $result = ['data' => $entity, 'success' => true];

        // bye!
        return $result;
    }

    /** {@inheritdoc} */
    public function create(array $post = [])
    {
        return $this->update($post, null, true);
    }

    /** {@inheritdoc} @param boolean $isNew The flag indicates we are creating or updating a record */
    public function update(array $post = [], $criteria = null, $isNew = false)
    {
        // do some checks
        if (empty($post))
        {
            throw new Exceptions\ServiceException('Your request is invalid');
        }

        /** @var IBaseEntity $entity */
        if ($isNew)
        {
            if (isset($post['ModifiedAt']))
            {
                $post['AddedAt'] = $post['ModifiedAt'];
            }

            if (isset($post['ModifiedBy']))
            {
                $post['AddedBy'] = $post['ModifiedBy'];
            }

            $entity = $this->getRepository()->entity;
        }
        else
        {
            if (null === $criteria)
            {
                $where = [];

                foreach ($this->getRepository()->pk as $v)
                {
                    if (isset($post[$v]))
                    {
                        $where[$v] = $post[$v];
                    }
                }

                if (!empty($where))
                {
                    $criteria = ['where' => $where];
                }
            }

            $result = $this->read($criteria);
            $entity = $result['data'];
        }

        $eventArgs = new Events\UpdateEventArgs($post, $entity, $isNew);
        $eventArgs->setPost(array_intersect_key($post, $entity->getReflection()->getDefaultProperties()));

        // exchange array & fire events if any
        $this->trigger(static::ON_EXCHANGE_ARRAY, $eventArgs);
        $eventArgs->setEntity($entity->exchangeArray($eventArgs->getPost()));
        $this->trigger(static::ON_VALIDATE, $eventArgs);

        // validate 'em
        if (false !== ($errors = $entity->validate()))
        {
            throw new Exceptions\ValidateException(implode(' ', $errors));
        }

        // update db
        try
        {
            // start a transaction & fire events if any
            if ($this->enableTransaction)
            {
                $this->getRepository()->beginTransaction();
            }

            $this->trigger(static::ON_BEFORE_SAVE, $eventArgs);

            // create or update entity
            if ($isNew)
            {
                $affectedRows = $this->getRepository()->create($entity, false);
            }
            else
            {
                $affectedRows = $this->getRepository()->update($entity, $criteria, false);
            }

            if (1 > $affectedRows)
            {
                throw new Exceptions\ServiceException('Error saving data');
            }

            // commit current transaction & fire events if any
            $this->trigger(static::ON_AFTER_SAVE, $eventArgs);
            $this->getRepository()->flush()->commit();

            // bye!
            if ($isNew)
            {
                $where = [];

                foreach ($this->getRepository()->pk as $v)
                {
                    $where[$v] = $entity->$v;
                }

                $criteria = ['where' => $where];
            }

            return $this->read($criteria);
        }
        catch (\Exception $ex)
        {
            // roll back current transaction
            $this->getRepository()->close()->rollBack();
            throw $ex;
        }
    }

    /** {@inheritdoc} */
    public function delete($criteria)
    {
        /** @var IBaseEntity $entity */
        $result = $this->read($criteria);
        $entity = $result['data'];

        // update db
        try
        {
            $eventArgs = new Events\UpdateEventArgs($criteria, $entity, false);

            // start a transaction & fire events if any
            if ($this->enableTransaction)
            {
                $this->getRepository()->beginTransaction();
            }

            $this->trigger(static::ON_BEFORE_DELETE, $eventArgs);

            // delete entity
            $affectedRows = $this->getRepository()->delete($entity, false);

            if (1 > $affectedRows)
            {
                throw new Exceptions\ServiceException('Error deleting data');
            }

            // commit current transaction & fire events if any
            $this->trigger(static::ON_AFTER_DELETE, $eventArgs);
            $this->getRepository()->flush()->commit();

            // bye!
            return ['success' => true];
        }
        catch (\Exception $ex)
        {
            // roll back current transaction
            $this->getRepository()->close()->rollBack();
            throw $ex;
        }
    }

    /** @var bool A value that indicates whether the transaction is enabled */
    public $enableTransaction = false;

    /** {@inheritdoc} */
    public function __get($key)
    {
        return property_exists($this, $key) ? $this->$key : $this->getRepository()->$key;
    }
}