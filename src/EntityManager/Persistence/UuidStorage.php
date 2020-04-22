<?php

declare(strict_types=1);

namespace App\EntityManager\Persistence;

use App\DataModel\Uuid;

class UuidStorage
{
    /**
     * Stores UUID into the storage.
     * Will throw Exception if UUID already exists.
     */
    public function allocate(Uuid $uuid): void
    {
        // @todo save UUID and confirm it is unique
    }
}
