<?php

declare(strict_types=1);

namespace App\Sharding;

class TypeCoordinator
{
    /**
     * Returns TRUE if valid EntityType or FALSE otherwise.
     */
    public function isValidType(int $entityTypeID): bool
    {
        return $entityTypeID >= 100 && $entityTypeID <= 2000;
    }
}
