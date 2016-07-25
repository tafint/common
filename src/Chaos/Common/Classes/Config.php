<?php namespace Chaos\Common\Classes;

/**
 * Class Config
 * @author ntd1712
 */
class Config extends \Noodlehaus\Config
{
    /** {@inheritdoc} */
    public function __construct($paths = [])
    {
        !is_array($paths) || !is_string(key($paths))
            ? parent::__construct($paths)
            : $this->data = array_replace_recursive($this->getDefaults(), $paths);
    }

    /** {@inheritdoc} */
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

    /** {@inheritdoc} */
    protected function getDefaults()
    {
        return [
            'app' => [
                'datetimeFormat' => 'Y-m-d H:i:s',
                'dateFormat' => 'Y-m-d',
                'timeFormat' => 'H:i:s',
                'itemsPerPage' => 10,
                'maxItemsPerPage' => 100,
                'minSearchChars' => 4,
                'charset' => 'UTF-8',
                'defaultPassword' => '******',
                'key' => ''
            ],
            'auth' => [
                'default' => 'none', // framework, oauth2, etc.
                'drivers' => [
                    'oauth2' => [
                        'clientId' => 'demoapp',
                        'clientSecret' => 'demopass',
                        'redirectUri' => 'http://example.com/your-redirect-url',
                        'urlAuthorize' => 'http://brentertainment.com/oauth2/lockdin/authorize',
                        'urlAccessToken' => 'http://brentertainment.com/oauth2/lockdin/token',
                        'urlResourceOwnerDetails' => 'http://brentertainment.com/oauth2/lockdin/resource',
                    ]
                ],
            ],
            'cookie' => [
                'path' => '/',
                'domain' => null,
                'expires' => 120,
                'secure' => false,
                'http_only' => true
            ],
            'multitenant' => [
                'enabled' => false,
                'keymap' => 'ApplicationKey'
            ],
            'orm' => [
                'walkers' => [
                    'doctrine.customOutputWalker' => 'Chaos\Doctrine\Walkers\CustomOutputWalker'
                ]
            ]
        ];
    }
}