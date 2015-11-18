<?php namespace Chaos\Common;

/**
 * Class AbstractBaseObjectItem
 * @author ntd1712
 */
abstract class AbstractBaseObjectItem extends AbstractBaseObject implements IBaseObjectItem
{
    use BaseObjectItemTrait;

    /**
     * Constructor
     *
     * @param   object|array $data
     */
    public function __construct($data = [])
    {
        if (!empty($data))
        {
            is_object($data) ? $this->exchangeObject($data) : $this->exchangeArray($data);
        }
    }

    /** {@inheritdoc} @override */
    public function fromJson($json, $assoc = false)
    {
        return $this->exchangeArray((array)call_user_func_array(['parent', __FUNCTION__], func_get_args()));
    }

    /** {@inheritdoc} @override */
    public function toArray()
    {
        return $this->objectToArray($this, 0);
    }

    /** {@inheritdoc} */
    public function toSimpleArray()
    {
        return get_object_vars($this);
    }

    /** {@inheritdoc} */
    public function exchangeArray(array $data, IBaseObjectItem $instance = null)
    {
        if (empty($data))
        {
            return $this;
        }

        $properties = (new \ReflectionClass($this))->getProperties();

        foreach ($properties as $property)
        {
            // do some checks
            if ($property->isStatic() ||
                false !== stripos($docComment = $property->getDocComment(), CHAOS_ANNOTATION_IGNORE))
            {
                continue;
            }

            if (!$property->isPublic())
            {
                $property->setAccessible(true);
            }

            // check if given name not exist in data
            if (!array_key_exists($property->name, $data))
            {
                $data[$property->name] = $property->getValue($this);
                // continue;
            }

            // do we have any defined TYPE(s)?
            $types = $this->getTypes($property);
            $isCollection = isset($types[1]);
            $value = $data[$property->name];

            // switch...
            if ($types['is_scalar'])
            {
                // type juggling
                if (false === stripos($docComment, CHAOS_ANNOTATION_IGNORE_TYPE_JUGGLING))
                {
                    $value = Types\Type::getType(strtolower($types[0]))->convertToPHPValue($value);
                }

                // do we have any defined filters & validators?
                if (false === stripos($docComment, CHAOS_ANNOTATION_IGNORE_RULES))
                {
                    $this->addRules($property);
                }
            }
            elseif (isset($instance) && is_object($instance) && $types[0] === get_class($instance))
            {   // check for circular object references
                if ($isCollection)
                {
                    $value = $this->addToCollection($instance, $types[1]);
                }
                else
                {
                    $value = &$instance;
                }
            }
            elseif (class_exists($types[0]) && is_array($value))
            {
                if (is_subclass_of($types[0], __NAMESPACE__ . '\IBaseObjectItem'))
                {
                    $obj = $isCollection ? $types[1] : null;

                    if (!empty($value) && false === stripos($docComment, CHAOS_ANNOTATION_IGNORE_DATA))
                    {   // @todo: need to optimize code
                        if ($isCollection)
                        {   /** @var IBaseObjectItem $cls */
                            $method = method_exists($obj, 'add') ? 'add' : 'append'; // guess supported method
                            $firstKey = key($value);
                            $isMulti = isset($value[$firstKey]) &&
                                (is_array($value[$firstKey]) || is_object($value[$firstKey]));

                            if (!$isMulti)
                            {
                                $value = [$value];
                            }

                            if (0 === iterator_count($obj))
                            {
                                foreach ($value as $v)
                                {
                                    $cls = new $types[0];
                                    is_object($v) ? ($v instanceof $cls ? $cls = $v :
                                        $cls->exchangeObject($v)) : $cls->exchangeArray($v, $this);

                                    $this->addToCollection($cls, $obj, $method);
                                }
                            }
                            else
                            {
                                $identifier = array_flip($this->getIdentifier());
                                $tmp = [];

                                foreach ($obj as $k => $v)
                                {
                                    if (is_object($v))
                                    {
                                        $v = get_object_vars($v);
                                    }

                                    if ($v = array_intersect_key($v, $identifier))
                                    {
                                        $tmp[$k] = $v;
                                    }
                                }

                                foreach ($value as $v)
                                {
                                    if (is_object($v))
                                    {
                                        $v = get_object_vars($v);
                                    }

                                    if (($v = array_intersect_key($v, $identifier)) &&
                                        false !== ($k = array_search($v, $tmp)))
                                    {
                                        if ($obj[$k] instanceof IBaseObjectItem)
                                        {
                                            $obj[$k]->exchangeArray($v, $this);
                                        }
                                        else
                                        {
                                            foreach ($obj[$k] as $key => $val)
                                            {
                                                if (array_key_exists($key, $v))
                                                {
                                                    $obj[$k]->$key = $v[$key];
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $cls = new $types[0];
                                        $cls->exchangeArray($v, $this);

                                        $this->addToCollection($cls, $obj, $method);
                                    }
                                }
                            }
                        }
                        else
                        {   /** @var IBaseObjectItem $obj */
                            $obj = new $types[0];
                            $obj->exchangeArray($value, $this);
                        }
                    }

                    $value = $obj;
                }
                else
                {
                    try
                    {   // unknown object, we use a kind of default instance
                        $obj = new $types[0]($value);

                        if ($isCollection)
                        {
                            $obj = $this->addToCollection($obj, $types[1]);
                        }

                        $value = $obj;
                    }
                    catch (\Exception $ex) {}
                }
            }

            // set our new value (if any)
            $property->setValue($this, $value);
            unset($data[$property->name]); // for next loop
        }

        return $this;
    }

    /** {@inheritdoc} @param bool $force */
    public function exchangeObject($data, $force = false)
    {
        if ($force)
        {
            $this->exchangeArray(get_object_vars($data));
        }
        else
        {
            foreach ($this as $key => $value)
            {
                if (property_exists($data, $key))
                {
                    $this->$key = $data->$key;
                }
            }
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function __clone()
    {
        foreach ($this as $key => $value)
        {
            if (is_object($value))
            {
                $this->$key = clone $value;
            }
        }
    }

    /** {@inheritdoc} */
    public function __get($key)
    {
        if (property_exists($this, $key))
        {
            return $this->$key;
        }

        $getter = 'get' . str_replace('_', '', $key);

        if (is_callable([$this, $getter]))
        {
            return $this->$getter();
        }

        throw new Exceptions\BadMethodCallException(sprintf(
            '"%s" does not have a callable "%s" getter method which must be defined',
            $key,
            'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))
        ));
    }

    /** {@inheritdoc} */
    public function __set($key, $value)
    {
        if (property_exists($this, $key))
        {
            $this->$key = $value;
            return;
        }

        $setter = 'set' . str_replace('_', '', $key);

        if (is_callable([$this, $setter]))
        {
            $this->$setter($value);
            return;
        }

        throw new Exceptions\BadMethodCallException(sprintf(
            '"%s" does not have a callable "%s" ("%s") setter method which must be defined',
            $key,
            'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))),
            $setter
        ));
    }

    /** {@inheritdoc} */
    public function __isset($key)
    {
        try
        {
            return null !== $this->__get($key);
        }
        catch (Exceptions\BadMethodCallException $e) {}

        return false;
    }

    /** {@inheritdoc} */
    public function __unset($key)
    {
        try
        {
            $this->__set($key, null);
        }
        catch (Exceptions\BadMethodCallException $e)
        {
            throw new Exceptions\InvalidArgumentException(
                'The class property $' . $key . ' cannot be unset as NULL is an invalid value for it', 0, $e);
        }
    }
}