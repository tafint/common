<?php namespace Chaos\Common\Traits;

use Chaos\Common\Types\Type;

/**
 * Trait AuditEntityTrait
 * @author ntd1712
 */
trait AuditEntityTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="added_at", type="datetime", nullable=true)
     * [IgnoreRules]
     */
    private $AddedAt;
    /**
     * @Doctrine\ORM\Mapping\Column(name="added_by", type="string", nullable=true)
     * [IgnoreRules]
     */
    private $AddedBy;
    /**
     * @Doctrine\ORM\Mapping\Column(name="edited_at", type="datetime", nullable=true)
     * [IgnoreRules]
     */
    private $EditedAt;
    /**
     * @Doctrine\ORM\Mapping\Column(name="edited_by", type="string", nullable=true)
     * [IgnoreRules]
     */
    private $EditedBy;
    /**
     * @Doctrine\ORM\Mapping\Column(name="is_deleted", type="boolean", nullable=true)
     * [IgnoreRules]
     */
    private $IsDeleted;
    /**
     * @Doctrine\ORM\Mapping\Column(name="version", type="integer", nullable=true)
     * @Doctrine\ORM\Mapping\Version
     * [IgnoreRules]
     */
    private $Version;

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
    public function getEditedAtDataType()
    {
        return Type::DATETIME_TYPE;
    }

    /**
     * @return \DateTime
     */
    public function getEditedAt()
    {
        return $this->EditedAt;
    }

    /**
     * @param \DateTime $EditedAt
     * @return $this
     */
    public function setEditedAt($EditedAt)
    {
        $this->EditedAt = $EditedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getEditedByDataType()
    {
        return Type::STRING_TYPE;
    }

    /**
     * @return string
     */
    public function getEditedBy()
    {
        return $this->EditedBy;
    }

    /**
     * @param string $EditedBy
     * @return $this
     */
    public function setEditedBy($EditedBy)
    {
        $this->EditedBy = $EditedBy;
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
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->IsDeleted;
    }

    /**
     * @param boolean $IsDeleted
     * @return $this
     */
    public function setIsDeleted($IsDeleted)
    {
        $this->IsDeleted = $IsDeleted;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersionDataType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->Version;
    }

    /**
     * @param int $Version
     * @return $this
     */
    public function setVersion($Version)
    {
        $this->Version = $Version;
        return $this;
    }
}