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
     * Only valid date strings or DateTime objects allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        if (!is_string($val) && !($val instanceof DateTimeInterface)) {
            return false;
        }

        if (is_string($val)) {
            try {
                $dateTime = new DateTime($val);

                return $this->isValidDate($dateTime);
            } catch (Exception $e) {
                return false;
            }
        }

        if ($val instanceof DateTimeInterface) {
            return $this->isValidDate($val);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (!$this->isValidValue($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId()));
        }
        if (is_string($val)) {
            return new DateTimeImmutable($val, new DateTimeZone('UTC'));
        }

        if ($val instanceof DateTime) {
            $val->setTimezone(new DateTimeZone('UTC'));

            return DateTimeImmutable::createFromMutable($val);
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
