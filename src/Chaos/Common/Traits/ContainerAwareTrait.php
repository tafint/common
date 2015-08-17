<?php namespace Chaos\Common\Traits;

use League\Container\ContainerInterface;
use League\Container\Container;

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
            $container = new Container($container);
        }

        self::$container = $container;
        return $this;
    }
}