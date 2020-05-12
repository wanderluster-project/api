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
    public function toArray(): array
    {
        $formattedVal = null;
        if ($this->val instanceof DateTimeImmutable) {
            $formattedVal = $this->val->format('c');
        }

        return [
            'type' => $this->getSerializationId(),
            'val' => $formattedVal,
            'ver' => $this->getVersion(),
        ];
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
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_COERSION_UNSUCCESSFUL, $val, $this->getSerializationId()));
        }
        if (is_string($val)) {
            return new DateTimeImmutable($val);
        }

        if($val instanceof DateTime){
            return DateTimeImmutable::createFromMutable($val);
        }

        return $val;
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
