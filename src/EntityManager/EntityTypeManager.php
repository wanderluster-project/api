<?php

declare(strict_types=1);

namespace App\EntityManager;

class EntityTypeManager
{
    /**
     * Returns TRUE if valid EntityType or FALSE otherwise.
     *
     * @var mixed
     */
    public function isValidType($entityTypeID): bool
    {
        if (is_null($entityTypeID)) {
            return false;
        }

        if (!is_int($entityTypeID)) {
            return false;
        }

        return $entityTypeID >= 0 && $entityTypeID <= 1000000;
    }
}
