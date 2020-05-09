<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Serializer\SerializableInterface;
use App\Exception\WanderlusterException;

interface TypeInterface extends SerializableInterface
{
    /**
     * Set the val for the DataType.
     *
     * @param mixed $val
     *
     * @throws WanderlusterException
     */
    public function setValue($val, array $options = []): self;

    /**
     * Set the val for the data type.
     *
     * @throws WanderlusterException
     *
     * @return mixed
     */
    public function getValue(array $options = []);

    /**
     * Set the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function setVersion(int $version): TypeInterface;

    /**
     * Get the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function getVersion(): int;

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

    /**
     * Merge data from an existing type.
     */
    public function merge(TypeInterface $type): void;
}
