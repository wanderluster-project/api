<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\Exception\WanderlusterException;

interface SerializableInterface
{
    /**
     * Returns the unique identifier used for serialization.
     */
    public function getSerializationId(): string;

    /**
     * Converts data type to array to be serialized.
     *
     * @throws WanderlusterException
     */
    public function toArray(): array;

    /**
     * Hydrates values from array.
     *
     * @throws WanderlusterException
     */
    public function fromArray(array $data): self;
}
