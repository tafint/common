<?php namespace Chaos\Common\Events;

/**
 * Class ReadEventArgs
 * @author ntd1712
 */
class ReadEventArgs extends EventArgs
{
    /** @var mixed */
    private $criteria;
    /** @var mixed */
    private $data;

    /**
     * Constructor
     *
     * @param   mixed $criteria
     * @param   mixed $data
     */
    public function __construct($criteria, $data = null)
    {
        $this->criteria = $criteria;
        $this->data = $data;
    }

    /**
     * @return  mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param   mixed $criteria
     * @return  $this
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
        return $this;
    }

    /**
     * @return  mixed|\Traversable|\Chaos\Common\IBaseEntity
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param   mixed $data
     * @return  $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}