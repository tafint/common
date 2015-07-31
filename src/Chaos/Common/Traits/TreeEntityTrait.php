<?php namespace Chaos\Common\Traits;

use Chaos\Common\Types\Type;

/**
 * Trait TreeEntityTrait
 * @author ntd1712
 */
trait TreeEntityTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="lft", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $Left;
    /**
     * @Doctrine\ORM\Mapping\Column(name="rgt", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $Right;
    /**
     * @Doctrine\ORM\Mapping\Column(name="dpt", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $Depth;
    /**
     * @Doctrine\ORM\Mapping\Column(name="parent_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $ParentId;

    /**
     * @return string
     */
    public function getLeftDataType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return  integer
     */
    public function getLeft()
    {
        return $this->Left;
    }

    /**
     * @param   integer $Left
     * @return  $this
     */
    public function setLeft($Left)
    {
        $this->Left = $Left;
        return $this;
    }

    /**
     * @return string
     */
    public function getRightDataType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return  integer
     */
    public function getRight()
    {
        return $this->Right;
    }

    /**
     * @param   integer $Right
     * @return  $this
     */
    public function setRight($Right)
    {
        $this->Right = $Right;
        return $this;
    }

    /**
     * @return string
     */
    public function getDepthDataType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return  integer
     */
    public function getDepth()
    {
        return $this->Depth;
    }

    /**
     * @param   integer $Depth
     * @return  $this
     */
    public function setDepth($Depth)
    {
        $this->Depth = $Depth;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentIdType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return  integer
     */
    public function getParentId()
    {
        return $this->ParentId;
    }

    /**
     * @param   integer $ParentId
     * @return  $this
     */
    public function setParentId($ParentId)
    {
        $this->ParentId = $ParentId;
        return $this;
    }
}