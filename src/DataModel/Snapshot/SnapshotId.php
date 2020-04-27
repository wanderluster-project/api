<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Entity\EntityId;
use App\DataModel\StringInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class SnapshotId implements StringInterface
{
    const PATTERN = '/^[0-9]*-[0-9]*-[0-9A-Fa-f]{16}\.[0-9]*$/';

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
     *
     * @throws WanderlusterException
     */
    public function __construct(string $snapshotIdString)
    {
        if (!preg_match(self::PATTERN, $snapshotIdString)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_SNAPSHOT_ID, $snapshotIdString));
        }

        $parts = explode('.', $snapshotIdString);
        $this->entityId = new EntityId($parts[0]);
        $this->version = (int) $parts[1];
    }

    /**
     * Get the EntityID associated with this Snapshot.
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get the version associated with this Snapshot.
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->asString();
    }

    /**
     * Convert to string.
     */
    public function asString(): string
    {
        return (string) $this->getEntityId().'.'.$this->getVersion();
    }
}
