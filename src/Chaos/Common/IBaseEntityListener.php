<?php namespace Chaos\Common;

/**
 * Interface IBaseEntityListener
 * @author ntd1712
 */
interface IBaseEntityListener
{
    /**
     * The "postLoad" event
     *
     * @param   IBaseEntity $entity
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     * @see     \Doctrine\ORM\Events
     */
    function postLoad($entity, $eventArgs);
}