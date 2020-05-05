<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\Exception\WanderlusterException;

interface SerializableInterface
{
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
