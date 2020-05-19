<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class IntegerType extends AbstractDataType
{
    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'INT';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof IntegerType;
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (is_null($val) || is_int($val)) {
            return $val;
        }

        if (is_float($val)) {
            $val = (int) round($val);
        }

        if (!is_int($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        return $val;
    }
}
