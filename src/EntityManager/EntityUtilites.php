<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Snapshot\Snapshot;
use ReflectionClass;

class EntityUtilites
{
    /**
     * @throws \ReflectionException
     */
    public function setEntityId(Entity $entity, EntityId $entityId): void
    {
        $reflection = new ReflectionClass(Entity::class);
        $prop = $reflection->getProperty('entityId');
        $prop->setAccessible(true);
        $prop->setValue($entity, $entityId);
    }

    /**
     * @throws \ReflectionException
     */
    public function setEntityType(Entity $entity, int $entityType): void
    {
        $entityRefl = new ReflectionClass(Entity::class);
        $snapshotRefl = new ReflectionClass(Snapshot::class);

        $snapshotProp = $entityRefl->getProperty('snapshot');
        $snapshotProp->setAccessible(true);
        $entityTypeProp = $snapshotRefl->getProperty('entityType');
        $entityTypeProp->setAccessible(true);

        /**
         * @var Snapshot $snapshot
         */
        $snapshot = $snapshotProp->getValue($entity);
        $entityTypeProp->setValue($snapshot, $entityType);
    }

    /**
     * @param string $lang
     *
     * @throws \ReflectionException
     */
    public function setLang(Entity $entity, $lang): void
    {
        $entityRefl = new ReflectionClass(Entity::class);
        $snapshotRefl = new ReflectionClass(Snapshot::class);

        $snapshotProp = $entityRefl->getProperty('snapshot');
        $snapshotProp->setAccessible(true);
        $langProp = $snapshotRefl->getProperty('lang');
        $langProp->setAccessible(true);

        /**
         * @var Snapshot $snapshot
         */
        $snapshot = $snapshotProp->getValue($entity);
        $langProp->setValue($snapshot, $lang);
    }
}
