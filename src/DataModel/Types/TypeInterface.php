<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Serializer\SerializableInterface;
use App\Exception\WanderlusterException;

interface TypeInterface extends SerializableInterface
{
    /**
     * Returns the identifier for this data type.
     */
    public function getTypeId(): string;

    /**
     * Set the val for the DataType.
     *
     * @param mixed $val
     *
     * @throws WanderlusterException
     */
    public function setValue($val, array $options = []): self;

    /**
     * Set the val for the DataType.
     *
     * @throws WanderlusterException
     *
     * @return mixed
     */
    public function getValue(array $options = []);

    /**
     * Returns TRUE if value is null, FALSE otherwise.
     *
     * @throws WanderlusterException
     */
    public function isNull(array $options = []): bool;

    /**
     * Returns array of language codes represented by the value of this data type.
     */
    public function getLanguages(): array;
}
