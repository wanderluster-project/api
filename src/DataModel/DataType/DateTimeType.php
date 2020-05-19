<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class DateTimeType extends AbstractDataType
{
    public function getSerializationId(): string
    {
        return 'DATE_TIME';
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
            }

            if ($val instanceof DateTime) {
                $val->setTimezone(new DateTimeZone('UTC'));
                $val = DateTimeImmutable::createFromMutable($val);
            }
        } catch (Exception $e) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        if (!$val instanceof DateTimeImmutable) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        if (!$this->isValidDate($val)) {
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

    /**
     * Validates that the DateTime object is a valid date.
     */
    protected function isValidDate(DateTimeInterface $dateTime): bool
    {
        return checkdate(
        // month
            (int) $dateTime->format('n'),
            // day
            (int) $dateTime->format('j'),
            // year
            (int) $dateTime->format('Y')
        );
    }
}
