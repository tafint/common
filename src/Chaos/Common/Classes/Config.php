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
                // specific
                'charset' => 'UTF-8',
                'defaultPassword' => '******',
                'fallback_locale' => 'en',
                'locale' => 'en',
                'key' => '',
                'url' => ''
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
            'multitenant' => [
                'enabled' => false,
                'keymap' => 'ApplicationKey'
            ],
            'session' => [
                'cookie' => 'chaos',
                'domain' => null
            ],
            'orm' => [
                'walkers' => [
                    'doctrine.customOutputWalker' => 'Chaos\Doctrine\Walkers\CustomOutputWalker'
                ]
            ]
        ];
    }
}