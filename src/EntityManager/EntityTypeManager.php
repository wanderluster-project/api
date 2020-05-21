<?php

declare(strict_types=1);

namespace App\EntityManager;

class EntityTypeManager
{
    /**
     * Returns TRUE if valid EntityType or FALSE otherwise.
     */
    public function isValidType(?int $entityTypeID): bool
    {
        if (is_null($entityTypeID)) {
            return false;
        }

        return $entityTypeID >= 0 && $entityTypeID <= 1000000;
    }
}
