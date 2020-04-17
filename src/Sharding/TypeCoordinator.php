<?php

namespace App\Sharding;

class TypeCoordinator
{
    public function isValidType(EntityType $type)
    {
        $id = $type->getId();
        return $id >= 100 && $id <= 2000;
    }
}
