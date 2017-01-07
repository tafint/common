<?php namespace Chaos\Common\Classes;

/**
 * Class Config
 * @author ntd1712
 * @deprecated
 */
class Config extends \Noodlehaus\Config
{
    /** {@inheritdoc} */
    public function __construct($paths = [])
    {
        if (empty($paths))
        {
            $this->data = $this->getDefaults();
        }
        else
        {
            !is_array($paths) || !is_string(key($paths))
                ? parent::__construct($paths)
                : $this->data = array_replace_recursive($this->getDefaults(), $paths);
        }
    }

    /** {@inheritdoc} @override */
    public function all(array $excludes = [])
    {
        if (!empty($excludes))
        {
            foreach ($excludes as $v)
            {
                array_unset($this->data, $v);
            }
        }

        return $this->data;
    }

    /** {@inheritdoc} @override */
    protected function getDefaults()
    {
        return [
            'app' => [
                'dateFormat' => 'Y-m-d',
                'timeFormat' => 'H:i:s',
                'itemsPerPage' => 10,
                'maxItemsPerPage' => 100,
                'minSearchChars' => 4,
                'charset' => 'UTF-8',
                'debug' => false,
                'defaultPassword' => '******',
                'fallback_locale' => 'en',
                'locale' => 'en',
                'key' => '',
                'url' => ''
            ],
            'auth' => [
                'default' => 'jwt',
                'drivers' => [
                    'jwt' => [
                        'algorithm' => 'HS256',
                        'secret' => 'demopass',
                        'refresh_ttl' => 1209600, // 2 weeks, in seconds
                        'ttl' => 7200, // 2 hours, in seconds
                    ],
                    'oauth2' => [
                        'clientId' => 'demoapp',
                        'clientSecret' => 'demopass',
                        'redirectUri' => 'http://localhost',
                        'urlAuthorize' => 'http://brentertainment.com/oauth2/lockdin/authorize',
                        'urlAccessToken' => 'http://brentertainment.com/oauth2/lockdin/token',
                        'urlResourceOwnerDetails' => 'http://brentertainment.com/oauth2/lockdin/resource',
                    ]
                ]
            ],
            'db' => [
                'driver' => 'pdo_mysql',
                'user' => 'forge',
                'password' => '',
                'host' => 'localhost',
                'port' => 3306,
                'dbname' => 'forge',
                'unix_socket' => null,
                'charset'   => 'utf8',
                'driverOptions' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                ],
                'prefix' => 'tbl_'
            ],
            'multitenant' => [
                'enabled' => false,
                'keymap' => 'ApplicationKey'
            ],
            'orm' => [
                'cache' => [
                    'provider' => 'array',
                    'file' => [
                        'directory' => 'storage/framework/cache',
                        'extension' => '.doctrinecache.data'
                    ],
                    'redis' => [
                        'host' => '127.0.0.1',
                        'port' => 6379,
                        'dbindex' => 1
                    ]
                ],
                'metadata' => [
                    'driver' => 'annotation',
                    'paths' => 'modules/doctrine.paths.php',
                    'simple' => false
                ],
                'proxy_classes' => [
                    'auto_generate' => 0,
                    'directory' => 'storage/framework/proxies',
                    'namespace' => null,
                ],
                'default_repository' => 'Doctrine\ORM\EntityRepository',
                'sql_logger' => null,
                'walkers' => [
                    'doctrine.customOutputWalker' => 'Chaos\Doctrine\Walkers\CustomOutputWalker'
                ]
            ],
            'session' => [
                'cookie' => 'chaos',
                'domain' => null
            ]
        ];
    }
}