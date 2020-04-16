<?php

namespace App\Sharding;

class TypeCoordinator
{
    public function isValidType(int $type)
    {
        return $type >= 100 && $type <= 2000;
    }
}
