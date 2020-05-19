<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

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
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (is_null($val) || is_float($val)) {
            return $val;
        }

        if (is_int($val)) {
            return (float) $val;
        }

        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
    }
}
