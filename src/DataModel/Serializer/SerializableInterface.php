<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\Exception\WanderlusterException;

interface SerializableInterface
{
    /**
     * Returns the identifier for this data type.
     */
    public function getTypeId(): string;

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
