<?php

declare(strict_types=1);

namespace App\EntityManager\Persistence;

use App\DataModel\Entity\EntityId;

class EntityIdStorage
{
    /**
     * Stores EntityID into the storage.
     * Will throw Exception if EntityID already exists.
     */
    public function allocate(EntityId $entityId): void
    {
        // @todo save entityID and confirm it is unique
    }
}
