<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;

class NumericType extends AbstractDataType
{
    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'NUM';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof NumericType;
    }

    /**
     * Only integer and floats allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        return is_int($val) || is_float($val);
    }
}
