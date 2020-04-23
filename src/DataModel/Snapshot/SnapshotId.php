<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Entity\EntityId;

class SnapshotId
{
    /**
     * @var EntityId
     */
    protected $entityId;

    /**
     * @var int
     */
    protected $version;

    /**
     * SnapshotId constructor.
     */
    public function __construct(EntityId $entityId, int $version)
    {
        $this->entityId = $entityId;
        $this->version = $version;
    }
}
