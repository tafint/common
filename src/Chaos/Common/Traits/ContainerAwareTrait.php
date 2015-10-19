<?php namespace Chaos\Common\Traits;

use League\Container\ContainerInterface;
use League\Container\Container;

/**
 * Trait ContainerAwareTrait
 * @author ntd1712
 */
trait ContainerAwareTrait
{
    /** @var Container|ContainerInterface */
    private static $container;

    /**
     * Resolve a given type from / or get the <tt>Container</tt> instance
     *
     * @param   string $alias
     * @param   array $args
     * @return  mixed|ContainerInterface
     */
    public function getContainer($alias = null, array $args = [])
    {
        return isset($alias) ? self::$container->get($alias, $args) : self::$container;
    }

    /**
     * Set a <tt>Container</tt> instance
     *
     * @param   array|\ArrayAccess|ContainerInterface $container
     * @return  $this
     */
    public function setContainer($container)
    {
        if (!$container instanceof ContainerInterface)
        {
            $definitions = $container;
            $container = new Container;

            if (is_array($definitions) || $definitions instanceof \ArrayAccess)
            {
                array_walk($definitions, [$container, 'addServiceProvider']);
            }
        }

        self::$container = $container;
        return $this;
    }
}