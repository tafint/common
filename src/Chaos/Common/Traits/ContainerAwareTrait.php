<?php namespace Chaos\Common\Traits;

use League\Container\ContainerInterface;
use League\Container\Container;
use Chaos\Common\Exceptions\InvalidArgumentException;
use Chaos\Common\Exceptions\RuntimeException;

/**
 * Trait ContainerAwareTrait
 * @author ntd1712
 */
trait ContainerAwareTrait
{
    /** @var ContainerInterface */
    private static $container;

    /**
     * Either resolve a given type from the <tt>Container</tt> or get the <tt>Container</tt> instance
     *
     * @param   string $alias
     * @param   array $args
     * @return  ContainerInterface|mixed
     */
    public function getContainer($alias = null, array $args = [])
    {
        return isset($alias) ? self::$container->get($alias, $args) : self::$container;
    }

    /**
     * Set a <tt>Container</tt> instance
     *
     * @param   ContainerInterface|array|\ArrayAccess $container
     * @return  $this
     */
    public function setContainer($container)
    {
        if (!$container instanceof ContainerInterface)
        {
            if (empty($container) || !is_array($container) && !$container instanceof \ArrayAccess)
            {
                throw new InvalidArgumentException(
                    'You can only load definitions from an array or an object that implements ArrayAccess.'
                );
            }
            elseif (!isset($container['di']) || !is_array($container['di']))
            {
                throw new RuntimeException(
                    'Could not process configuration, either the top level key [di] is missing or the configuration is not an array.'
                );
            }

            $definitions = $container['di'];
            $container = new Container;

            foreach ($definitions as $k => $v)
            {
                $container->add($v['definition']);
                $container->add($k, $v['definition']);
            }
        }

        self::$container = $container;
        return $this;
    }
}