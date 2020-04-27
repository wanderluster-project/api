<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\StringInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class EntityId implements StringInterface
{
    const PATTERN = '/^[0-9]*-[0-9]*-[0-9A-Fa-f]{16}$/';

    /**
     * @SerializedName("customer_name")
     *
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
    public function __construct(string $entityIdString)
    {
        if (!preg_match(self::PATTERN, $entityIdString)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_ID, $entityIdString));
        }

        $parts = explode('-', $entityIdString);
        $this->shard = (int) $parts[0];
        $this->type = (int) $parts[1];
        $this->identifier = $parts[2];
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
        return $this->getShard().'-'.$this->getEntityType().'-'.$this->getIdentifier();
    }

    /**
     * Return the string representation.
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
