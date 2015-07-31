<?php namespace Chaos\Common\Traits;

use Chaos\Common\Types\Type;

/**
 * Trait AuditEntityTrait
 * @author ntd1712
 */
trait AuditEntityTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="added_at", type="datetime", nullable=true, options={"version":true})
     * [IgnoreRules]
     */
    private $AddedAt;
    /**
     * @Doctrine\ORM\Mapping\Column(name="added_by", type="string", nullable=true)
     * [IgnoreRules]
     */
    private $AddedBy;
    /**
     * @Doctrine\ORM\Mapping\Column(name="modified_at", type="datetime", nullable=true, options={"version":true})
     * [IgnoreRules]
     */
    private $ModifiedAt;
    /**
     * @Doctrine\ORM\Mapping\Column(name="modified_by", type="string", nullable=true)
     * [IgnoreRules]
     */
    private $ModifiedBy;
    /**
     * @Doctrine\ORM\Mapping\Column(name="is_deleted", type="boolean", nullable=true)
     * [IgnoreRules]
     */
    private $IsDeleted;

    /**
     * @return string
     */
    public function getAddedAtDataType()
    {
        return Type::DATETIME_TYPE;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt()
    {
        return $this->AddedAt;
    }

    /**
     * @param \DateTime $AddedAt
     * @return $this
     */
    public function setAddedAt($AddedAt)
    {
        $this->AddedAt = $AddedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddedByDataType()
    {
        return Type::STRING_TYPE;
    }

    /**
     * @return string
     */
    public function getAddedBy()
    {
        return $this->AddedBy;
    }

    /**
     * @param string $AddedBy
     * @return $this
     */
    public function setAddedBy($AddedBy)
    {
        $this->AddedBy = $AddedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedAtDataType()
    {
        return Type::DATETIME_TYPE;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->ModifiedAt;
    }

    /**
     * @param \DateTime $ModifiedAt
     * @return $this
     */
    public function setModifiedAt($ModifiedAt)
    {
        $this->ModifiedAt = $ModifiedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedByDataType()
    {
        return Type::STRING_TYPE;
    }

    /**
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->ModifiedBy;
    }

    /**
     * @param string $ModifiedBy
     * @return $this
     */
    public function setModifiedBy($ModifiedBy)
    {
        $this->ModifiedBy = $ModifiedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsDeletedDataType()
    {
        return Type::BOOLEAN_TYPE;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->IsDeleted;
    }

    /**
     * @param bool $IsDeleted
     * @return $this
     */
    public function setIsDeleted($IsDeleted)
    {
        $this->IsDeleted = $IsDeleted;
        return $this;
    }
}