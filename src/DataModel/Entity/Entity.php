<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Validation\Constraints;

class Entity
{
    /**
     * @var EntityId
     */
    protected $entityId;

    /**
     * @var Constraints
     */
    protected $constraints;

    /**
     * @var Snapshot
     */
    protected $snapshot;

    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    public function getSnapshot(): Snapshot
    {
        return $this->snapshot;
    }

    public function getConstraints(): Constraints
    {
        return $this->constraints;
    }
}
