<?php namespace Chaos\Common\Events;

use Chaos\Common\IBaseEntity;

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
    /** @var IBaseEntity */
    private $entity;
    /** @var IBaseEntity */
    private $master;
    /** @var boolean */
    private $isNew;

    /**
     * Constructor
     *
     * @param   mixed $post
     * @param   IBaseEntity $entity
     * @param   boolean $isNew
     */
    public function __construct($post, IBaseEntity $entity, $isNew)
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
     * @return  mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return  mixed
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
     * @return  mixed|IBaseEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param   IBaseEntity $entity
     * @return  $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return  mixed|IBaseEntity
     */
    public function getMaster()
    {
        return $this->master;
    }
}