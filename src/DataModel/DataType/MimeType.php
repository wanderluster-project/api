<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;

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
}
