<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
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
}
