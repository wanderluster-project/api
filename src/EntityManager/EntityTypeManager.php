<?php

declare(strict_types=1);

namespace App\EntityManager;

class EntityTypeManager
{
    /**
     * Returns TRUE if valid EntityType or FALSE otherwise.
     */
    public function isValidType(int $entityTypeID): bool
    {
        return $entityTypeID >= 0 && $entityTypeID <= 1000000;
    }
}
