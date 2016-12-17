<?php namespace Chaos\Doctrine\Extensions;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs,
    Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class TablePrefix
 * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */
class TablePrefix
{
    /** @var string */
    protected $prefix;

    /**
     * Constructor
     */
    public function __construct($prefix)
    {
        $this->prefix = (string)$prefix;
    }

    /**
     * @param   LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName)
        {
            $classMetadata->setPrimaryTable(['name' => $this->prefix . $classMetadata->getTableName()]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping)
        {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide'])
            {
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix
                    . $mapping['joinTable']['name'];
            }
        }
    }
}