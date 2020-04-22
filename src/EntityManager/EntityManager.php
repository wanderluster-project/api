<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity;

class EntityManager
{
    public function create(): Entity
    {
        return new Entity();
    }
}
