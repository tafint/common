<?php namespace Chaos\Common\Events;

/**
 * Class UpdateEventArgs
 * @author ntd1712
 */
class UpdateEventArgs extends EventArgs
{
    /** @var mixed */
    private $payload;
    /** @var mixed */
    private $post;
    /** @var mixed */
    private $entity;
    /** @var mixed */
    private $master;
    /** @var boolean */
    private $isNew;

    /**
     * Constructor
     *
     * @param   mixed $post
     * @param   mixed $entity
     * @param   boolean $isNew
     */
    public function __construct($post, $entity, $isNew)
    {
        $this->isNew = $isNew;
        $this->post = $this->payload = $post;
        $this->entity = $entity;

        if (null !== $entity)
        {
            $this->master = clone $entity;
        }
    }

    /**
     * @return  boolean
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @return  mixed|array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return  mixed|array
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param   mixed $post
     * @return  $this
     */
    public function setPost($post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return  mixed|\Chaos\Common\IBaseEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param   mixed $entity
     * @return  $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return  mixed|\Chaos\Common\IBaseEntity
     */
    public function getMaster()
    {
        return $this->master;
    }
}