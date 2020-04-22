<?php

declare(strict_types=1);

namespace App\Sharding;

use JsonSerializable;
use Serializable;

class Uuid implements Serializable, JsonSerializable
{
    /**
     * @var string
     */
    protected $uuid;

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
     * Uuid constructor.
     */
    public function __construct(string $uuid)
    {
        $this->init($uuid);
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
        return $this->uuid;
    }

    /**
     * Serialize the UUID to string.
     */
    public function serialize(): string
    {
        return $this->uuid;
    }

    /**
     * Convert string --> UUID parts.
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $this->init($serialized);
    }

    /**
     * Serialize the UUID to string.
     */
    public function jsonSerialize(): string
    {
        return $this->uuid;
    }

    /**
     * Parse UUID into parts and load state.
     *
     * @param string $uuid
     */
    protected function init($uuid): void
    {
        $this->uuid = $uuid;
        $uuidParts = explode('-', $uuid);
        $this->shard = (int) $uuidParts[0];
        $this->type = (int) $uuidParts[1];
        $this->identifier = $uuidParts[2];
    }
}
