<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Entity\EntityId;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class SnapshotId
{
    const PATTERN = '/^(.*)\.([a-z]{2})-([0-9]+)$/';

    /**
     * @var EntityId
     */
    protected $entityId;

    /**
     * @var string
     */
    protected $lang;

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

        preg_match(self::PATTERN, $snapshotIdString, $matches);

        $this->entityId = new EntityId($matches[1]);
        $this->lang = $matches[2];
        $this->version = (int) $matches[3];
    }

    /**
     * Get the UUID of the entity associated with this snapshot.
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get the language associated with this snapshot.
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * Get the version associated with this snapshot.
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Convert to string.
     */
    public function asString(): string
    {
        return (string) $this->getEntityId().'.'.$this->getLanguage().'-'.$this->getVersion();
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
