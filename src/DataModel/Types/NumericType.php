<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\Exception\ErrorMessages;
use App\Exception\TypeError;
use App\Exception\WanderlusterException;

class NumericType implements DataTypeInterface
{
    /**
     * @var int|float|null
     */
    protected $val;

    /**
     * Numeric constructor.
     *
     * @param float|int|null $val
     *
     * @throws WanderlusterException
     */
    public function __construct($val = null, array $options = [])
    {
        $this->setValue($val, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(): string
    {
        return 'NUM';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getTypeId(),
            'val' => $this->val,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): DataTypeInterface
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
    public function setValue($val, array $options = []): DataTypeInterface
    {
        if (!is_int($val) && !is_float($val) && !is_null($val)) {
            throw new TypeError(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'Numeric required'));
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
}
