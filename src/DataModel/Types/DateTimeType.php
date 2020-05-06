<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Serializer\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use Exception;

class DateTimeType implements TypeInterface
{
    /**
     * @var DateTimeImmutable|null
     */
    protected $val;

    /**
     * DateTimeType constructor.
     *
     * @param string|DateTime|DateTimeImmutable|null $val
     */
    public function __construct($val = null, array $options = [])
    {
        $this->setValue($val, $options);
    }

    public function getTypeId(): string
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
            'type' => $this->getTypeId(),
            'val' => $formattedVal,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'val'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $val = $data['val'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }
        $this->setValue($val);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): TypeInterface
    {
        if (is_string($val)) {
            try {
                $val = new DateTimeImmutable($val);
            } catch (Exception $e) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'Invalid date string'));
            }
        }

        if ($val instanceof DateTime) {
            $val = DateTimeImmutable::createFromMutable($val);
        }

        if (!($val instanceof DateTimeImmutable) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'DateTime required'));
        }

        $this->val = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $options = [])
    {
        return $this->val;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(array $options = []): bool
    {
        return is_null($this->getValue($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        return [LanguageCodes::ANY];
    }
}
