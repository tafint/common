<?php namespace Chaos\Common\Traits;

use League\Container\Container,
    League\Container\ContainerInterface;

/**
 * Trait ContainerAwareTrait
 * @author ntd1712
 * @deprecated No longer use
 */
trait ContainerAwareTrait
{
    /** @var ContainerInterface */
    private static $__container__;

    /**
     * Get a reference to the container object. The object returned will be of type <tt>ContainerInterface</tt>
     *
     * @return  ContainerInterface
     */
    public function getContainer()
    {
        return self::$__container__;
    }

    /**
     * Set a reference to the container object
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

        self::$__container__ = $container;
        return $this;
    }
}