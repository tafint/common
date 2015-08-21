<?php namespace Chaos\Common;

/**
 * Class BaseObjectItemTrait
 * @author ntd1712
 */
trait BaseObjectItemTrait
{
    /** {@inheritdoc} */
    public function getReflection()
    {
        return new \ReflectionClass($this);
    }

    /**
     * Get property type(s)
     *
     * @param   \ReflectionProperty $property
     * @return  array
     */
    private function getTypes(\ReflectionProperty $property)
    {
        $getter = 'get' . $property->name . 'DataType';

        // check if getXyzDataType() method exists
        if (method_exists($this, $getter))
        {
            $types = @call_user_func([$this, $getter]);

            if (!is_array($types))
            {
                $types = [$types];
            }
        }
        else // otherwise, guess it
        {
            $docComment = $property->getDocComment();

            if (false !== stripos($docComment, 'orm\\') || false !== stripos($docComment, 'customcolumn'))
            {   // e.g. @Doctrine\ORM\Mapping\Column(type="integer")
                //  return ['integer']
                if (0 === preg_match(CHAOS_MATCH_COLUMN_TYPE, $docComment, $types))
                {   // e.g. @Doctrine\ORM\Mapping\Column(columnDefinition="tinyint(4) DEFAULT NULL")
                    //  return ['tinyint']
                    if (0 === preg_match(CHAOS_MATCH_COLUMN_DEFINITION, $docComment, $types))
                    {   // e.g. @Doctrine\ORM\Mapping\OneToMany(targetEntity="Channel")
                        //  return ['Doctrine\Common\Collections\ArrayCollection', 'Channel'];
                        preg_match(CHAOS_MATCH_ONE_MANY, $docComment, $types);

                        if (isset($types[1]) && isset($types[2]))
                        {
                            if ('OneToMany' === $types[1] || 'ManyToMany' === $types[1])
                            {
                                $types[1] = DOCTRINE_ARRAY_COLLECTION;
                            }
                        }
                    }
                }
            }
            elseif (false !== stripos($docComment, '@var'))
            {   // e.g. @var int
                //  return ['int']
                // e.g. @var \Doctrine\Common\Collections\ArrayCollection(Channel)
                //  return ['Doctrine\Common\Collections\ArrayCollection', 'Channel'];
                preg_match(CHAOS_MATCH_VAR, $docComment, $types);
            }

            if (!empty($types))
            {
                array_shift($types);
            }
        }

        if (empty($types))
        {
            return [gettype($property->getValue($this)), 'is_scalar' => true];
        }

        // parse the found "types[1]" if any
        $scalars = Types\Type::getTypesMap();

        if (isset($types[1]))
        {   // e.g. ['Channel', 'Doctrine\Common\Collections\ArrayCollection'];
            $types = array_reverse($types);
            $value = $property->getValue($this);

            // check if this property has been instanced
            if (isset($value))
            {
                $types[1] = $value;
            }
            elseif (!isset($scalars[strtolower($types[1])])) // ...and not a scalar type
            {
                if (false === strpos($types[1], '\\')) // use default namespace for secondary type (if any)
                {
                    $types[1] = $property->getDeclaringClass()->getNamespaceName() . '\\' . $types[1];
                }

                if (is_subclass_of($types[1], __NAMESPACE__ . '\IBaseObjectCollection'))
                {
                    $types[1] = new $types[1];
                }
                elseif (class_exists($types[1], false))
                {   // unknown object, we use a kind of default instance
                    $types[1] = (new \ReflectionClass($types[1]))->newInstanceWithoutConstructor();
                }
            }

            // currently we only support type \Traversable
            if (!$types[1] instanceof \Traversable)
            {
                unset($types[1]);
            }
        }

        // define default namespace for primary type if any
        $types['is_scalar'] = isset($scalars[strtolower($types[0])]);

        if (!$types['is_scalar'] && false === strpos($types[0], '\\'))
        {
            $types[0] = $property->getDeclaringClass()->getNamespaceName() . '\\' . $types[0];
        }

        // bye!
        return $types;
    }

    /**
     * Add property filter & validator rules
     *
     * @param   \ReflectionProperty $property
     * @return  $this
     */
    private function addRules(\ReflectionProperty $property)
    {
        $getter = 'get' . $property->name . 'DataRules';

        // check if getXyzDataRules() method exists
        /** @var IBaseEntity $this */
        if (method_exists($this, $getter))
        {
            return $this->addRule($property, @call_user_func([$this, $getter]));
        }

        // otherwise, guess it
        preg_match_all(CHAOS_MATCH_RULE, $property->getDocComment(), $matches);

        if (isset($matches[1]))
        {
            foreach ($matches[1] as $v)
            {
                $values = explode('|', $v); // e.g. [NotEmpty|StringLength('max' => 255, 'message' => '{property} too long')]

                foreach ($values as $value)
                {
                    $this->addRule($property, '[' . trim($value, " \t\n\r\0\x0B[]") . ']');
                }
            }
        }

        // bye!
        return $this;
    }

    /**
     * Add an item at the end of a collection
     *
     * @param   object $item
     * @param   \Traversable $collection
     * @param   string $method
     * @return  \Traversable
     */
    private function addToCollection($item, \Traversable $collection, $method = null)
    {
        if (null === $method) // guess supported collection method
        {
            $method = method_exists($collection, 'add') ? 'add' : 'append';
        }

        call_user_func([$collection, $method], $item);

        return $collection;
    }

    /**
     * Convert object to array
     *
     * @param   mixed $data
     * @param   int $depth Depth that we go into; defaults to 0
     * @param   array $visited An array of visited objects; used to prevent cycling
     * @return  mixed
     * @tutorial Breadth-first search
     */
    private function objectToArray($data, $depth, &$visited = [])
    {
        if (empty($data))
        {
            return $data;
        }

        if (is_object($data))
        {
            // do some checks
            /*if (8388608 < memory_get_usage())
            {
                if (!gc_enabled())
                {
                    gc_enable();
                }

                set_time_limit(0);
                gc_collect_cycles();
            }*/

            $className = get_class($data);
            $hash = spl_object_hash($data);

            if (isset($visited[$hash]) || CHAOS_RECURSION_MAX_DEPTH <= $depth/* ||
               (isset($visited[$className]) && CHAOS_RECURSION_MIN_DEPTH <= $depth)*/) // @fixme
            {
                return '*RECURSION(' . str_replace('\\', '\\\\', $className) . '#' . $depth . ')*';
            }

            $visited[$className] = $visited[$hash] = true;

            // cast object to array
            if ($data instanceof \Traversable)
            {
                if (is_a($data, DOCTRINE_PERSISTENT_COLLECTION) && is_subclass_of($data->getOwner(), DOCTRINE_PROXY))
                {
                    return '*RECURSION(' . str_replace('\\', '\\\\', $className) . '#' . $depth . ')*';
                }

                if ($data instanceof IBaseObjectCollection || method_exists($data, 'toArray'))
                {   // IBaseObjectCollection, Doctrine\Common\Collections\Collection
                    $vars = $data->toArray();
                }
                elseif (method_exists($data, 'getArrayCopy'))
                {   // ArrayObject, ArrayIterator
                    $vars = $data->getArrayCopy();
                }
                else
                {
                    $vars = [];

                    foreach ($data as $v)
                    {
                        $vars[] = $v;
                    }
                }

            }
            else
            {
                $vars = get_object_vars($data);

                if (is_subclass_of($data, DOCTRINE_PROXY))
                {
                    unset($vars['__initializer__'], $vars['__cloner__'], $vars['__isInitialized__']);
                }
            }

            $data = $vars;
        }

        if (is_array($data))
        {
            return array_map(function($item) use($depth, $visited) {
                return $this->objectToArray($item, $depth + 1, $visited);
            }, $data);
        }

        return $data;
    }
}