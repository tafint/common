<?php namespace Chaos\Common;

/**
 * Class AbstractBaseObjectCollection
 * @author ntd1712
 *
 * @see Doctrine\Common\Collections\ArrayCollection
 */
abstract class AbstractBaseObjectCollection extends AbstractBaseObject
    implements \Countable, \IteratorAggregate, \ArrayAccess, IBaseObjectCollection
{
    /** @var array An array containing the items of this collection */
    private $items;

    /**
     * Constructor
     *
     * @param   array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /** {@inheritdoc} */
    public function count()
    {
        return count($this->items);
    }

    /** {@inheritdoc} */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /** {@inheritdoc} */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value)
    {
        return $this->containsKey($offset) ? $this->set($offset, $value) : $this->add($value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /** {@inheritdoc} @override */
    public function toArray()
    {
        return $this->items;
    }

    /** {@inheritdoc} */
    public function add($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /** {@inheritdoc} */
    public function append($item)
    {
        return $this->add($item);
    }

    /** {@inheritdoc} */
    public function prepend($item)
    {
        array_unshift($this->items, $item);
        return $this;
    }

    /** {@inheritdoc} */
    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /** {@inheritdoc} */
    public function contains($item)
    {
        return in_array($item, $this->items, true);
    }

    /** {@inheritdoc} */
    public function containsKey($key)
    {
        return isset($this->items[$key]) || array_key_exists($key, $this->items);
    }

    /** {@inheritdoc} */
    public function current()
    {
        return current($this->items);
    }

    /** {@inheritdoc} */
    public function first()
    {
        return reset($this->items);
    }

    /** {@inheritdoc} */
    public function last()
    {
        return end($this->items);
    }

    /** {@inheritdoc} */
    public function next()
    {
        return next($this->items);
    }

    /** {@inheritdoc} */
    public function exists(\Closure $closure)
    {
        foreach ($this->items as $key => $item)
        {
            if ($closure($key, $item))
            {
                return true;
            }
        }

        return false;
    }

    /** {@inheritdoc} */
    public function filter(\Closure $closure)
    {
        return new static(array_filter($this->items, $closure));
    }

    /** {@inheritdoc} */
    public function forAll(\Closure $closure)
    {
        foreach ($this->items as $key => $item)
        {
            if (!$closure($key, $item))
            {
                return false;
            }
        }

        return true;
    }

    /** {@inheritdoc} */
    public function get($key)
    {
        return $this->containsKey($key) ? $this->items[$key] : null;
    }

    /** {@inheritdoc} */
    public function getArrayCopy()
    {
        return $this->toArray();
    }

    /** {@inheritdoc} */
    public function getKeys()
    {
        return array_keys($this->items);
    }

    /** {@inheritdoc} */
    public function getValues()
    {
        return array_values($this->items);
    }

    /** {@inheritdoc} */
    public function indexOf($item)
    {
        return array_search($item, $this->items, true);
    }

    /** {@inheritdoc} */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /** {@inheritdoc} */
    public function key()
    {
        return key($this->items);
    }

    /** {@inheritdoc} */
    public function map(\Closure $closure)
    {
        return new static(array_map($closure, $this->items));
    }

    /** {@inheritdoc} */
    public function partition(\Closure $closure)
    {
        $matches = $noMatches = [];

        foreach ($this->items as $key => $item)
        {
            if ($closure($key, $item))
            {
                $matches[$key] = $item;
            }
            else
            {
                $noMatches[$key] = $item;
            }
        }

        return [new static($matches), new static($noMatches)];
    }

    /** {@inheritdoc} */
    public function remove($offset)
    {
        if ($this->offsetExists($offset))
        {
            $removed = $this->items[$offset];
            unset($this->items[$offset]);

            return $removed;
        }

        return null;
    }

    /** {@inheritdoc} */
    public function removeItem($item)
    {
        $offset = $this->indexOf($item);

        if (false !== $offset)
        {
            unset($this->items[$offset]);
            return true;
        }

        return false;
    }

    /** {@inheritdoc} */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
        return $this;
    }

    /** {@inheritdoc} */
    public function slice($offset, $length = null)
    {
        return array_slice($this->items, $offset, $length, true);
    }
}