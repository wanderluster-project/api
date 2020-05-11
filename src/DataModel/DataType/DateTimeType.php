<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use Exception;

class DateTimeType extends AbstractDataType
{
    protected ?DateTimeImmutable $val;

    /**
     * DateTimeType constructor.
     *
     * @param string|DateTime|DateTimeImmutable|null $val
     *
     * @throws WanderlusterException
     */
    public function __construct($val = null, array $options = [])
    {
        $this->setValue($val, $options);

        $ver = isset($options['ver']) ? (int) $options['ver'] : 0;
        $this->setVersion($ver);
    }

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
    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'val', 'ver'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $val = $data['val'];
        $ver = (int) $data['ver'];

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }
        $this->setValue($val);
        $this->setVersion($ver);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
        if (is_string($val)) {
            try {
                $val = new DateTimeImmutable($val);
            } catch (Exception $e) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'Invalid date string'));
            }
        }

        if ($val instanceof DateTime) {
            $val = DateTimeImmutable::createFromMutable($val);
        }

        if (!($val instanceof DateTimeImmutable) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'DateTime required'));
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

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof DateTimeType;
    }
}
