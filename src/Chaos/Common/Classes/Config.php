<?php namespace Chaos\Common\Classes;

/**
 * Class Config
 * @author ntd1712
 */
class Config extends \Noodlehaus\Config
{
    /** {@inheritdoc} */
    public function __construct($path)
    {
        is_array($path) ? \Noodlehaus\AbstractConfig::__construct($path) : parent::__construct($path);
    }

    /**
     * Get all of the configuration settings
     *
     * @param   bool $strict
     * @return  array
     */
    public function all($strict = false)
    {
        if ($strict)
        {
            foreach ($this->data as $k => $v)
            {
                if (file_exists($v))
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