<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\Exception\TypeError;
use App\Exception\WanderlusterException;

interface DataTypeInterface
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
     *
     * @return DataTypeInterface
     */
    public function fromArray(array $data): self;

    /**
     * Set the val for the DataType.
     *
     * @param mixed $val
     *
     * @throws TypeError
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
