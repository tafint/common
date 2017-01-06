<?php namespace Chaos\Common\Traits;

use Doctrine\ORM\Events;

/**
 * Trait RepositoryAwareTrait
 * @author ntd1712
 *
 * @method \Noodlehaus\ConfigInterface getConfig()
 * @method \Symfony\Component\DependencyInjection\ContainerBuilder getContainer()
 */
trait RepositoryAwareTrait
{
    /** @var array */
    private static $__repositories__ = [];

    /**
     * Get a reference to the repository object. The object returned will be of type <tt>IBaseRepository</tt>
     *  $this->getService()->getRepository('User')->...
     *  $this->getService('User')->getRepository('Role')->...
     *  $this->getService('Account\Services\UserService')->getRepository('Account\Entities\Role')->...
     *
     * @param   string $name The repository name; defaults to get_called_class()
     * @param   boolean $cache; defaults to TRUE
     * @return  mixed|\Chaos\Common\AbstractDoctrineRepository|\Chaos\Common\IDoctrineRepository|\Chaos\Common\IBaseRepository
     */
    public function getRepository($name = null, $cache = true)
    {
        if (empty($name))
        {
            $name = str_replace(['Repository', 'Service'], '', trim(strrchr(get_called_class(), '\\'), '\\'));
            $repositoryName = $name . 'Repository';
        }
        else
        {
            $repositoryName = $name;
        }

        if (isset(self::$__repositories__[$repositoryName]) && $cache)
        {
            return self::$__repositories__[$repositoryName];
        }

        self::$__repositories__[$repositoryName] = $this->getContainer()->get(DOCTRINE_ENTITY_MANAGER)
            ->getRepository(get_class($this->getContainer()->get($name)))
            ->setContainer($container = $this->getContainer())
            ->setConfig($config = $this->getConfig());

        // inject some stuffs into the entity
        foreach (self::$__repositories__[$repositoryName]->metadata->entityListeners as $k => $v)
        {
            if (Events::postLoad === $k) // to ensure this runs only once when called
            {
                foreach ($v as $listener)
                {
                    self::$__repositories__[$repositoryName]->entityManager->getConfiguration()->getEntityListenerResolver()
                    ->register($container->get($listener['class'])
                        ->setContainer($container)
                        ->setConfig($config)
                    );
                }
            }
        }

        return self::$__repositories__[$repositoryName];
    }
}