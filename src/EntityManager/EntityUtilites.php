<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use ReflectionClass;

class EntityUtilites
{
    public function setEntityId(EntityId $entityId, Entity $entity): void
    {
        $reflection = new ReflectionClass($entity);
        $prop = $reflection->getProperty('entityId');
        $prop->setAccessible(true);
        $prop->setValue($entity, $entityId);
    }
}
