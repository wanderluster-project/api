<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\DataModel\Serializer\Serializer;
use App\Exception\WanderlusterException;

interface DataTypeInterface extends SerializableInterface
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
     * Serialize a value to a string.
     *
     * @return mixed $val
     *
     * @throws WanderlusterException
     */
    public function getSerializedValue();

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
     * Set the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function setVersion(int $version): self;

    /**
     * Get the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function getVersion(): int;

    /**
     * Returns TRUE if the data can be used and FALSE otherwise.
     *
     * @param mixed|null $val
     */
    public function isValidValue($val): bool;

    /**
     * Returns TRUE if identical and FALSE otherweise.
     *
     * @throws WanderlusterException
     */
    public function isEqualTo(DataTypeInterface $type): bool;

    /**
     * Returns TRUE if greater than $type and FALSE otherwise.
     *
     * @throws WanderlusterException
     */
    public function isGreaterThan(DataTypeInterface $type): bool;

    /**
     * Returns TRUE if able to merge and FALSE otherwise.
     */
    public function canMergeWith(DataTypeInterface $type): bool;

    /**
     * Merge data from an existing type.
     */
    public function merge(DataTypeInterface $type): self;

    /**
     * Does a type coersion or throws an Exception.
     *
     * @param mixed $val
     *
     * @return mixed
     *
     * @throws WanderlusterException
     */
    public function coerce($val);

    /**
     * Set the serializer.
     *
     * @return $this
     */
    public function setSerializer(Serializer $serializer): self;
}
