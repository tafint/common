<?php namespace Chaos\Common;

/**
 * Class AbstractBaseService
 * @author ntd1712
 */
abstract class AbstractBaseService implements IBaseService
{
    use BaseServiceTrait, Traits\ConfigAwareTrait, Traits\ContainerAwareTrait;

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
        $response = ['data' => [], 'total' => count($entities), 'success' => true];

        if (0 !== $response['total'])
        {
            $model = $this->getModel();

            if ($model['exist'])
            {
                foreach ($entities as $entity)
                {
                    $response['data'][] = new $model['name']($entity);
                }
            }
            else
            {
                $response['data'] = $entities instanceof \Traversable ? iterator_to_array($entities) : $entities;
            }
        }

        // bye!
        return $response;
    }

    /** {@inheritdoc} */
    public function read($criteria)
    {
        // do some checks
        if (is_scalar($criteria))
        {
            if (is_numeric($criteria))
            {
                $criteria = (int)$criteria;
            }
            else
            {
                $criteria = $this->filter($criteria);
            }

            if (empty($criteria) || 1 > $criteria)
            {
                throw new Exceptions\ServiceException('Your request is invalid');
            }

            if (0 !== count($pk = $this->getRepository()->pk))
            {
                $instance = $this->getRepository()->criteria;

                foreach ($pk as $v)
                {
                    $instance->orWhere($instance->expr()->eq($v, $criteria));
                }

                $criteria = $instance;
            }
            else
            {   // entity without identity? you must be kidding me!
                $criteria = null;
            }
        }

        if (empty($criteria))
        {
            throw new Exceptions\InvalidArgumentException(__METHOD__ . ' expects "$criteria" in array format');
        }

        // get item
        $entity = $this->getRepository()->read($criteria);

        if (!$entity)
        {
            throw new Exceptions\ServiceException('Your request is invalid');
        }

        // prepare data for output
        $model = $this->getModel();
        $response = [
            'data' => $model['exist'] ? new $model['name']($entity) : $entity,
            'success' => true
        ];

        // bye!
        return $response;
    }

    /** {@inheritdoc} */
    public function create(array $post = [])
    {
        return $this->update($post, null, true);
    }

    /** {@inheritdoc} */
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
            if (null === $criteria && 0 !== count($pk = $this->getRepository()->pk))
            {
                $where = [];

                foreach ($pk as $v)
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

        $argv = ['isNew' => $isNew, 'payload' => $post, 'post' => $post, 'entity' => $entity, 'master' => clone $entity];
        $argv['post'] = array_intersect_key($argv['post'], $entity->getReflection()->getDefaultProperties());

        // fire "onExchangeArray" (if any)
        if ($this->fireEvent(static::ON_EXCHANGE_ARRAY, $argv) && !$entity instanceof $argv['entity'])
        {
            $entity = $argv['entity'];
        }

        // exchange array
        $entity->exchangeArray($argv['post'], $entity);

        // fire "onValidate" event (if any)
        $this->fireEvent(static::ON_VALIDATE, $argv);

        // validate 'em
        if (false !== ($errors = $entity->validate()))
        {
            throw new Exceptions\ValidateException(implode(' ', $errors));
        }

        // update db
        try
        {
            // start a transaction
            $this->getRepository()->beginTransaction();
            // fire "onBeforeSave" event (if any)
            $this->fireEvent(static::ON_BEFORE_SAVE, $argv);

            // create or update entity
            if ($isNew)
            {
                $affectedRows = $this->getRepository()->create($entity);
            }
            else
            {
                $affectedRows = $this->getRepository()->update($entity, $criteria);
            }

            $argv['success'] = 0 !== $affectedRows;

            // fire "onAfterSave" event (if any)
            $this->fireEvent(static::ON_AFTER_SAVE, $argv);
            // commit current transaction & clear unit of work (if any)
            $this->getRepository()->commit()->refine();

            // bye!
            $where = [];

            foreach ($this->getRepository()->pk as $v)
            {
                $where[$v] = $entity->$v;
            }

            $response = $this->read(['where' => $where]);
            $response['success'] = $argv['success'];

            return $response;
        }
        catch (\Exception $ex)
        {
            // roll back current transaction
            $this->getRepository()->rollBack();
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
            $argv = ['post' => $criteria, 'entity' => $entity, 'master' => clone $entity];

            // start a transaction
            $this->getRepository()->beginTransaction();
            // fire "onBeforeDelete" event (if any)
            $this->fireEvent(static::ON_BEFORE_DELETE, $argv);

            // delete entity
            $affectedRows = $this->getRepository()->delete($entity);
            $argv['success'] = 0 !== $affectedRows;

            // fire "onAfterDelete" event (if any)
            $this->fireEvent(static::ON_AFTER_DELETE, $argv);
            // commit current transaction
            $this->getRepository()->commit();

            // bye!
            return ['success' => $argv['success']];
        }
        catch (\Exception $ex)
        {
            // roll back current transaction
            $this->getRepository()->rollBack();
            throw $ex;
        }
    }

    /** {@inheritdoc} */
    public function __call($name, $arguments)
    {
        switch ($name)
        {
            case 'getRepository':
            case 'getService':
            case 'getUser':
                return call_user_func_array($this->$name, $arguments);
            default:
                throw new Exceptions\BadMethodCallException(sprintf('Unknown method "%s::%s"', get_called_class(), $name));
        }
    }

    /** {@inheritdoc} */
    public function __get($key)
    {
        return property_exists($this, $key) ? $this->$key : $this->getRepository()->$key;
    }
}