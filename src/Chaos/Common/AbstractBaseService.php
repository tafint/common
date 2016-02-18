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
        // fire "onBeforeReadAll" event if any
        $eventArgs = new Events\ReadEventArgs(func_get_args());
        $this->fireEvent(static::ON_BEFORE_READ_ALL, $eventArgs);

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
        $response = ['data' => [], 'total' => count($entities), 'success' => true];

        if (0 !== $response['total'])
        {
            // fire "onAfterReadAll" event if any
            $this->fireEvent(static::ON_AFTER_READ_ALL, $eventArgs->setData($entities));
            // copy the iterator into an array
            $response['data'] = $entities instanceof \Traversable ? iterator_to_array($entities) : $entities;
        }

        // bye!
        return $response;
    }

    /** {@inheritdoc} */
    public function read($criteria)
    {
        // do some checks
        if ($isScalar = is_scalar($criteria))
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
                $criteria = $this->filter($criteria, true);

                if (empty($criteria))
                {
                    throw new Exceptions\ServiceException('Your request is invalid');
                }
            }
        }
        elseif (empty($criteria))
        {
            throw new Exceptions\InvalidArgumentException(__METHOD__ . ' expects "$criteria" in array format');
        }

        // fire "onBeforeRead" event if any
        $eventArgs = new Events\ReadEventArgs($criteria);
        $this->fireEvent(static::ON_BEFORE_READ, $eventArgs);

        // get item
        if ($isScalar)
        {
            $entity = $this->getRepository()->find($criteria);
        }
        else
        {
            $entity = $this->getRepository()->read($criteria);
        }

        if (null === $entity)
        {
            throw new Exceptions\ServiceException('Your request is invalid');
        }

        // fire "onAfterRead" event if any
        $this->fireEvent(static::ON_AFTER_READ, $eventArgs->setData($entity));
        // prepare data for output
        $response = ['data' => $entity, 'total' => 1, 'success' => true];

        // bye!
        return $response;
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
            $entity = $this->getRepository()->entity;

            if (isset($post['ModifiedAt']))
            {
                $post['AddedAt'] = $post['ModifiedAt'];
            }

            if (isset($post['ModifiedBy']))
            {
                $post['AddedBy'] = $post['ModifiedBy'];
            }
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

            $response = $this->read($criteria);
            $entity = $response['data'];
        }

        $eventArgs = new Events\UpdateEventArgs($post, $entity, $isNew);
        $eventArgs->setPost(array_intersect_key($post, $entity->getReflection()->getDefaultProperties()));

        // exchange array & fire events if any
        $this->fireEvent(static::ON_EXCHANGE_ARRAY, $eventArgs);
        $eventArgs->setEntity($entity->exchangeArray($eventArgs->getPost()));
        $this->fireEvent(static::ON_VALIDATE, $eventArgs);

        // validate 'em
        if (false !== ($errors = $entity->validate()))
        {
            throw new Exceptions\ValidateException(implode(' ', $errors));
        }

        // update db
        try
        {
            // start a transaction & fire "onBeforeSave" event if any
            $this->getRepository()->beginTransaction();
            $this->fireEvent(static::ON_BEFORE_SAVE, $eventArgs);

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

            // fire "onAfterSave" event if any & commit current transaction
            $this->fireEvent(static::ON_AFTER_SAVE, $eventArgs);
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
        $response = $this->read($criteria);
        $entity = $response['data'];

        // update db
        try
        {
            $eventArgs = new Events\UpdateEventArgs($criteria, $entity, false);

            // start a transaction & fire "onBeforeDelete" event if any
            $this->getRepository()->beginTransaction();
            $this->fireEvent(static::ON_BEFORE_DELETE, $eventArgs);

            // delete entity
            $affectedRows = $this->getRepository()->delete($entity, false);

            if (1 > $affectedRows)
            {
                throw new Exceptions\ServiceException('Error deleting data');
            }

            // fire "onAfterDelete" event if any & commit current transaction
            $this->fireEvent(static::ON_AFTER_DELETE, $eventArgs);
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

    /** {@inheritdoc} */
    public function __get($key)
    {
        return property_exists($this, $key) ? $this->$key : $this->getRepository()->$key;
    }
}