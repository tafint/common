<?php namespace Chaos\Common\Events;

/**
 * Class EventArgs
 * @author ntd1712
 */
class EventArgs
{
    /** @var array */
    private $results = [];

    /**
     * @return  array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param   string $key
     * @param   mixed $value;
     * @return  $this
     */
    public function addResult($key, $value)
    {
        $this->results[$key] = $value;
        return $this;
    }

    /**
     * @return  $this
     */
    public function clearResult()
    {
        $this->results = [];
        return $this;
    }
}