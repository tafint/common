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
    public function all($except = [])
    {
        if (!empty($except))
        {
            foreach ($except as $v)
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
                'defaultPassword' => '******',
                'imageAllowedExt' => 'gif,jpeg,jpg,png',
                'imageMaxSize' => 2097152,
                'itemsPerPage' => 10,
                'maxItemsPerPage' => 100,
                'minSearchChars' => 4
            ],
            'orm' => [
                'walkers' => [
                    'doctrine.customOutputWalker' => 'Chaos\Doctrine\Walkers\CustomOutputWalker'
                ]
            ]
        ];
    }
}