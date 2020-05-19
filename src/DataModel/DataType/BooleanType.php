<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class BooleanType extends AbstractDataType
{
    const SERIALIZATION_ID = 'BOOL';

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return self::SERIALIZATION_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof BooleanType;
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (is_bool($val) || is_null($val)) {
            return $val;
        }

        if ('TRUE' === $val || 1 === $val || 'T' === $val) {
            $val = true;
        } elseif ('FALSE' === $val || 0 === $val || 'F' === $val) {
            $val = false;
        }

        if (is_bool($val)) {
            return $val;
        }

        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
    }
}
