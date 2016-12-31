<?php namespace Chaos\Doctrine;

use Doctrine\Common\Cache,
    Doctrine\Common\EventManager,
    Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Mapping\Driver\XmlDriver,
    Doctrine\ORM\Mapping\Driver\YamlDriver,
    Doctrine\ORM\Tools\Setup,
    Chaos\Common\Exceptions\RuntimeException,
    Chaos\Common\Traits\ConfigAwareTrait,
    Chaos\Doctrine\Extensions\TablePrefix;

/**
 * Class EntityManagerFactory
 * @author ntd1712
 */
class EntityManagerFactory
{
    use ConfigAwareTrait;

    /** @var EntityManager|\Doctrine\ORM\EntityManagerInterface */
    protected static $entityManager;

    /**
     * @return  EntityManager|\Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        if (null === self::$entityManager)
        {
            self::$entityManager = EntityManager::create($this->getDbParams(),
                $this->getConfiguration($this->getCacheProvider()), $this->getEventManager());
        }

        return self::$entityManager;
    }

    /**
     * Create an instance of the class
     *
     * @return  $this
     */
    public static function create()
    {
        return new static;
    }

    /**
     * @return  array
     */
    protected function getDbParams()
    {
        $db = $this->getConfig()->get('db');
        $drivers = (new \ReflectionClass(DOCTRINE_DRIVER_MANAGER))->getStaticProperties()['driverSchemeAliases'];

        if (isset($drivers[$db['driver']]))
        {
            $db['driver'] = $drivers[$db['driver']];
        }

        return $db;
    }

    /**
     * @return  Cache\Cache
     */
    protected function getCacheProvider()
    {
        $config = $this->getConfig()->get('orm.cache');

        switch ($config['provider'])
        {
            case 'array':
                return new Cache\ArrayCache;
            case 'file':
                return new Cache\FilesystemCache($config[$config['provider']]['directory'],
                    $config[$config['provider']]['extension']);
            case 'redis':
                $redis = new \Redis;
                $redis->connect($config[$config['provider']]['host'], $config[$config['provider']]['port']);
                $redis->select($config[$config['provider']]['dbindex']);

                $cache = new Cache\RedisCache;
                $cache->setRedis($redis);

                return $cache;
            case 'memcached':
                $memcache = new \Memcache;
                $memcache->connect($config[$config['provider']]['host'], $config[$config['provider']]['port'],
                    $config[$config['provider']]['weight']);

                $cache = new Cache\MemcacheCache;
                $cache->setMemcache($memcache);

                return $cache;
            default:
                return null;
        }
    }

    /**
     * @param   Cache\Cache $cache
     * @return  Configuration
     */
    protected function getConfiguration(Cache\Cache $cache = null)
    {
        $orm = $this->getConfig()->get('orm');
        $configuration = Setup::createConfiguration($this->getConfig()->get('app.debug'),
            $orm['proxy_classes']['directory'], $cache);

        $configuration->setMetadataDriverImpl(self::getMetadataDriver($configuration, $orm['metadata']));
        $configuration->setCustomNumericFunctions([
            'ACOS' => 'DoctrineExtensions\Query\Mysql\Acos',
            'ASIN' => 'DoctrineExtensions\Query\Mysql\Asin',
            'ATAN' => 'DoctrineExtensions\Query\Mysql\Atan',
            'ATAN2' => 'DoctrineExtensions\Query\Mysql\Atan2',
            'COS' => 'DoctrineExtensions\Query\Mysql\Cos',
            'COT' => 'DoctrineExtensions\Query\Mysql\Cot',
            'DEGREES' => 'DoctrineExtensions\Query\Mysql\Degrees',
            'RADIANS' => 'DoctrineExtensions\Query\Mysql\Radians',
            'SIN' => 'DoctrineExtensions\Query\Mysql\Sin',
            'TAN' => 'DoctrineExtensions\Query\Mysql\Tan'
        ]);

        if (null !== $cache)
        {
            $configuration->setMetadataCacheImpl($cache);
            $configuration->setQueryCacheImpl($cache);
            $configuration->setResultCacheImpl($cache);
        }

        if (isset($orm['proxy_classes']['namespace']))
        {
            $configuration->setProxyNamespace($orm['proxy_classes']['namespace']);
        }

        $configuration->setAutoGenerateProxyClasses($orm['proxy_classes']['auto_generate']);
        $configuration->setDefaultRepositoryClassName($orm['default_repository']);
        $configuration->setSQLLogger($orm['sql_logger']);

        return $configuration;
    }

    /**
     * @param   Configuration $config
     * @param   array $metadata
     * @return  \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     * @throws  RuntimeException
     */
    protected static function getMetadataDriver(Configuration $config, $metadata)
    {
        switch ($metadata['driver'])
        {
            case 'annotation':
                return $config->newDefaultAnnotationDriver($metadata['paths'], $metadata['simple']);
            case 'yaml':
                return new YamlDriver($metadata['paths']);
            case 'xml':
                return new XmlDriver($metadata['paths']);
            case 'static':
                return new StaticPHPDriver($metadata['paths']);
            default:
                throw new RuntimeException(sprintf('Unsupported driver: %s', $metadata['driver']));
        }
    }

    /**
     * @return  EventManager
     */
    protected function getEventManager()
    {
        $eventManager = new EventManager;

        if ($prefix = $this->getConfig()->get('db.prefix'))
        {
            $eventManager->addEventListener(Events::loadClassMetadata, new TablePrefix($prefix));
        }

        return $eventManager;
    }
}