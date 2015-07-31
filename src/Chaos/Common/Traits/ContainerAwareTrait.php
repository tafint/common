<?php namespace Chaos\Common\Traits;

use League\Container\ContainerInterface;

/**
 * Trait ContainerAwareTrait
 * @author ntd1712
 */
trait ContainerAwareTrait
{
    /** @var ContainerInterface */
    private static $container;

    /**
     * Either resolve a given type from the <tt>container</tt> or get the <tt>container</tt> instance
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
     * Set a <tt>container</tt> instance
     *
     * @param   ContainerInterface $container
     * @return  $this
     */
    public function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
        return $this;
    }
}