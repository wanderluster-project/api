<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use JsonSerializable;
use Serializable;

class EntityId implements Serializable, JsonSerializable
{
    /**
     * @var string
     */
    protected $entityId;

    /**
     * @var int
     */
    protected $shard;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * EntityId constructor.
     *
     * @throws WanderlusterException
     */
    public function __construct(string $entityId)
    {
        $this->init($entityId);
    }

    /**
     * Get the shard where this object is stored.
     */
    public function getShard(): int
    {
        return $this->shard;
    }

    /**
     * Get the type of this object.
     */
    public function getEntityType(): int
    {
        return $this->type;
    }

    /**
     * Get the Identifier for this object.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Return the string representation.
     */
    public function asString(): string
    {
        return $this->__toString();
    }

    /**
     * Return the string representation.
     */
    public function __toString(): string
    {
        return $this->entityId;
    }

    /**
     * Serialize the EntityID to string.
     */
    public function serialize(): string
    {
        return $this->entityId;
    }

    /**
     * Convert string --> EntityId parts.
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $this->init($serialized);
    }

    /**
     * Serialize the EntityID to string.
     */
    public function jsonSerialize(): string
    {
        return $this->entityId;
    }

    /**
     * Parse EntityId into parts and load state.
     *
     * @param string $entityId
     *
     * @throws WanderlusterException
     */
    protected function init($entityId): void
    {
        if (!preg_match('/^[0-9]*-[0-9]*-[0-9A-Fa-f]{16}$/', $entityId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_ID, $entityId));
        }

        $this->entityId = $entityId;
        $parts = explode('-', $entityId);
        $this->shard = (int) $parts[0];
        $this->type = (int) $parts[1];
        $this->identifier = $parts[2];
    }
}
