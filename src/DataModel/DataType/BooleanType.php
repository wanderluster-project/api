<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;

class BooleanType extends AbstractDataType
{
    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'BOOL';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof BooleanType;
    }

    /**
     * Only boolean data accepted.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        return is_bool($val);
    }
}
