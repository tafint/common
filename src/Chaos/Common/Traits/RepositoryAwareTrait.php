<?php namespace Chaos\Common\Traits;

use Doctrine\ORM\Events;

/**
 * Trait RepositoryAwareTrait
 * @author ntd1712
 *
 * @method mixed|\Noodlehaus\ConfigInterface getConfig($key = null, $default = null)
 * @method mixed getContainer($alias = null, array $args = [])
 */
trait RepositoryAwareTrait
{
    /** @var array */
    private static $repositories = [];

    /**
     * Get the <tt>repository</tt> instance
     *  $this->getService()->getRepository('User')->...
     *  $this->getService('User')->getRepository('Role')->...
     *  $this->getService('Account\Services\UserService')->getRepository('Account\Entities\Role')->...
     *
     * @param   string $name The repository name; defaults to get_called_class()
     * @param   bool $cache; defaults to TRUE
     * @return  mixed|\Chaos\Common\AbstractDoctrineRepository|\Chaos\Common\IBaseRepository
     */
    public function getRepository($name = null, $cache = true)
    {
        if (empty($name) || false === strpos($name, '\\'))
        {
            $name = preg_replace(CHAOS_REPLACE_CLASS_SUFFIX, '$1', $name ?: get_called_class());
            $repositoryName = $name . 'Repository';
        }
        else
        {
            $repositoryName = $name;
        }

        if (isset(self::$repositories[$repositoryName]) && $cache)
        {
            return self::$repositories[$repositoryName];
        }

        self::$repositories[$repositoryName] = $this
            ->getContainer(DOCTRINE_ENTITY_MANAGER)
            ->getRepository(get_class($this->getContainer($name)))
            ->setContainer($this->getContainer())
            ->setConfig($this->getConfig());

        // inject some stuffs into the entity
        foreach (self::$repositories[$repositoryName]->metadata->entityListeners as $k => $v)
        {
            if (Events::postLoad === $k)
            {
                foreach ($v as $listener)
                {
                    self::$repositories[$repositoryName]->entityManager->getConfiguration()->getEntityListenerResolver()
                    ->register($this
                        ->getContainer($listener['class'])
                        ->setContainer($this->getContainer())
                        ->setConfig($this->getConfig())
                    );
                }
            }
        }

        return self::$repositories[$repositoryName];
    }
}