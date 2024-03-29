<?php

namespace Phoxx\Core\Database\Doctrine\Events;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class TablePrefix
{
    protected $prefix;

    public function __construct(string $prefix = null)
    {
        $this->prefix = $prefix;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        if (!$this->prefix) {
            return;
        }

        $classMetadata = $eventArgs->getClassMetadata();

        if (
            !$classMetadata->isInheritanceTypeSingleTable() ||
            $classMetadata->name === $classMetadata->rootEntityName
        ) {
            $classMetadata->table['name'] = $this->prefix . $classMetadata->table['name'];
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide']) {
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] =
                    $this->prefix . $mapping['joinTable']['name'];
            }
        }
    }
}
