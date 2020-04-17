<?php

namespace App\Sharding;

class TypeCoordinator
{
    public function isValidType(int $entityTypeID)
    {
        return $entityTypeID >= 100 && $entityTypeID <= 2000;
    }
}
