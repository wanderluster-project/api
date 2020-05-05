<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\Exception\ErrorMessages;
use App\Exception\InvalidEntityIdFormatException;
use App\Exception\WanderlusterException;

class EntityId
{
    /**
     * UUID Pattern with 8-4-4-4-12 hexdigits.
     * ex: c0db5fe7-24be-4e77-ad4c-ce1aaa5c7682.
     */
    const PATTERN = '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/';

    /**
     * @SerializedName("customer_name")
     *
     * @var string
     */
    protected $uuid;

    /**
     * EntityId constructor.
     *
     * @throws WanderlusterException
     */
    public function __construct(string $uuid)
    {
        if (!preg_match(self::PATTERN, $uuid)) {
            throw new InvalidEntityIdFormatException(sprintf(ErrorMessages::INVALID_ENTITY_ID, $uuid));
        }

        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Return the string representation.
     */
    public function asString(): string
    {
        return $this->getUuid();
    }

    /**
     * Return the string representation.
     */
    public function __toString(): string
    {
        return $this->getUuid();
    }
}
