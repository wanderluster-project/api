<?php

declare(strict_types=1);

namespace App\Sharding;

class UuidStorage
{
    /**
     * Stores UUID into the storage.
     * Will throw Exception if UUID already exists
     * @param $uuid
     */
    public function allocate($uuid)
    {
        // @todo save UUID and confirm it is unique
    }
}
