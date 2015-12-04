<?php namespace Chaos\Common\Classes;

/**
 * Class Config
 * @author ntd1712
 */
class Config extends \Noodlehaus\Config
{
    /** {@inheritdoc} */
    public function __construct($path = [])
    {
        is_array($path) && is_string(key($path)) ?
            $this->data = array_replace_recursive($this->getDefaults(), $path) :
            parent::__construct($path);
    }

    /** {@inheritdoc} */
    public function all($excludes = [])
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
                'charset' => 'UTF-8',
                'itemsPerPage' => 10,
                'maxItemsPerPage' => 100,
                'minSearchChars' => 4,
                'defaultPassword' => '******',
                'key' => ''
            ],
            'auth' => [
                'default' => 'database', // or oauth2, etc
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
                'secure' => false
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