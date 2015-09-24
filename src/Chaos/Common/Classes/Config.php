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
        is_array($path) && !@is_string($path[0]) ?
            \Noodlehaus\AbstractConfig::__construct($path) : parent::__construct($path);
    }

    /** {@inheritdoc} */
    public function all($except = [])
    {
        if (!empty($except))
        {
            foreach ($this->data as $k => $v)
            {
                if (in_array($k, $except))
                {
                    unset($this->data[$k]);
                }
            }
        }

        return $this->data;
    }

    /** {@inheritdoc} */
    protected function getDefaults()
    {
        return [
            'datetimeFormat' => 'Y-m-d H:i:s',
            'dateFormat' => 'Y-m-d',
            'timeFormat' => 'H:i:s',
            'timezone' => 'Asia/Saigon',
            'locale' => 'en_US.UTF-8',
            'charset' => 'UTF-8',
            'defaultPassword' => '******',
            'imageAllowedExt' => 'gif,jpeg,jpg,png',
            'imageMaxSize' => 2097152, // 2MB
            'itemsPerPage' => 10,
            'maxItemsPerPage' => 100,
            'minSearchChars' => 4,
            'superUserId' => 1
        ];
    }
}