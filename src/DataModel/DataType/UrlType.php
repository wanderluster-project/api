<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class UrlType extends AbstractDataType
{
    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'URL';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof UrlType;
    }

    /**
     * Only URL formatted strings allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        if (!is_string($val)) {
            return false;
        }

        return false !== filter_var($val, FILTER_VALIDATE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (!$this->isValidValue($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId()));
        }

        if (is_null($val)) {
            return $val;
        }

        return (string) $val;
    }
}
