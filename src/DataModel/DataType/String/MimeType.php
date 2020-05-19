<?php

declare(strict_types=1);

namespace App\DataModel\DataType\String;

use App\DataModel\Contracts\AbstractStringType;
use App\DataModel\Contracts\DataTypeInterface;

class MimeType extends AbstractStringType
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
     * {@inheritdoc}
     */
    public function isValidPattern(string $val): bool
    {
        return preg_match(self::PATTERN, $val) > 0;
    }
}
