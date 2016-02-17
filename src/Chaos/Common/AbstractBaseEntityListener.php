<?php namespace Chaos\Common;

/**
 * Class AbstractBaseEntityListener
 * @author ntd1712
 */
abstract class AbstractBaseEntityListener implements IBaseEntityListener
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait;

    /** {@inheritdoc} */
    public function postLoad($entity, $eventArgs)
    {
        $entity->setIdentifier($eventArgs->getEntityManager()->getUnitOfWork()->getEntityIdentifier($entity))
               ->setContainer($this->getContainer())
               ->setConfig($this->getConfig());
    }
}