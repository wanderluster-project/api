<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;

class NullType extends AbstractDataType
{
    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'NULL';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof NullType;
    }

    /**
     * Only NULL allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        return is_null($val);
    }
}
