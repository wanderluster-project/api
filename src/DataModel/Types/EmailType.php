<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class EmailType implements DataTypeInterface
{
    /**
     * @var string|null
     */
    protected $val;

    /**
     * EmailType constructor.
     *
     * @param string|null $val
     */
    public function __construct($val = null)
    {
        $this->setValue($val);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(): string
    {
        return 'EMAIL';
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
        if (!is_string($val) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'String required'));
        }

        if (is_string($val)) {
            if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'Invalid Email'));
            }
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
        return ['*'];
    }
}
