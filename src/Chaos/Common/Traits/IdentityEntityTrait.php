<?php namespace Chaos\Common\Traits;

use Chaos\Common\Types\Type;

/**
 * Trait IdentityEntityTrait
 * @author ntd1712
 */
trait IdentityEntityTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer", options={"unsigned"=true})
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @Doctrine\ORM\Mapping\Id
     * [IgnoreRules]
     */
    protected $Id;
    /**
     * @Doctrine\ORM\Mapping\Column(name="uuid", type="guid", nullable=true)
     * [IgnoreRules]
     */
    protected $Uuid;
    /**
     * @Doctrine\ORM\Mapping\Column(name="application_key", type="string", nullable=true)
     * [IgnoreRules]
     */
    private $ApplicationKey;

    /**
     * @return string
     */
    public function getIdDataType()
    {
        return Type::INTEGER_TYPE;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param int $Id
     * @return $this
     */
    public function setId($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuidDataType()
    {
        return Type::UUID_TYPE;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->Uuid;
    }

    /**
     * @param string $Uuid
     * @return $this
     */
    public function setUuid($Uuid)
    {
        $this->Uuid = $Uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationKeyDataType()
    {
        return Type::STRING_TYPE;
    }

    /**
     * @return string
     */
    public function getApplicationKey()
    {
        return $this->ApplicationKey;
    }

    /**
     * @param string $ApplicationKey
     * @return $this
     */
    public function setApplicationKey($ApplicationKey)
    {
        $this->ApplicationKey = $ApplicationKey;
        return $this;
    }
}