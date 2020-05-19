<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class DateTimeType extends AbstractDataType
{
    const SERIALIZATION_ID = 'DATE_TIME';

    public function getSerializationId(): string
    {
        return self::SERIALIZATION_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof DateTimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if ($val instanceof DateTimeImmutable || is_null($val)) {
            return $val;
        }

        try {
            if (is_string($val)) {
                $val = new DateTimeImmutable($val, new DateTimeZone('UTC'));
                $errors = DateTime::getLastErrors();
                if ($errors['warning_count'] > 0) {
                    throw new Exception('Error parsing date string.');
                }
            } elseif ($val instanceof DateTime) {
                $val->setTimezone(new DateTimeZone('UTC'));
                $val = DateTimeImmutable::createFromMutable($val);
            //@phpstan-ignore-next-line
            } elseif (!$val instanceof DateTimeImmutable) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
            }
        } catch (Exception $e) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializedValue()
    {
        if ($this->val instanceof DateTimeImmutable) {
            return $this->val->format('c');
        }

        return null;
    }
}
