<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class MimeType extends AbstractDataType
{
    const PATTERN = '/^[-\w]+\/[-\w\.\+]+$/';

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'MIME_TYPE';
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof MimeType;
    }

    /**
     * Only valid mime type strings allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        if (is_string($val)) {
            return preg_match(self::PATTERN, $val) > 0;
        }

        return false;
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
