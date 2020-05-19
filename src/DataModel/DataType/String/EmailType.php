<?php

declare(strict_types=1);

namespace App\DataModel\DataType\String;

use App\DataModel\Contracts\AbstractStringType;
use App\DataModel\Contracts\DataTypeInterface;

class EmailType extends AbstractStringType
{
    const SERIALIZATION_ID = 'EMAIL';

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
        return $type instanceof EmailType;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidPattern(string $val): bool
    {
        return false !== filter_var($val, FILTER_VALIDATE_EMAIL);
    }
}
