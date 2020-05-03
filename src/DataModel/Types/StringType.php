<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\Exception\ErrorMessages;
use App\Exception\TypeError;
use App\Exception\WanderlusterException;

class StringType implements DataTypeInterface
{
    /**
     * Associative array identifying languageCode => translation.
     *
     * @var string[]|null
     */
    protected $trans;

    /**
     * Boolean constructor.
     *
     * @param string[] $trans
     */
    public function __construct(array $trans = [])
    {
        foreach ($trans as $lang => $val) {
            $this->setValue($val, ['lang' => $lang]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(): string
    {
        return 'STRING';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getTypeId(),
            'trans' => $this->trans,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): DataTypeInterface
    {
        $fields = ['type', 'trans'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $trans = $data['trans'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }

        if (!is_array($trans)) {
            throw new TypeError(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'trans should be an array.'));
        }

        foreach ($trans as $lang => $val) {
            $this->setValue($val, ['lang' => $lang]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
        if (!is_string($val) && !is_null($val)) {
            throw new TypeError(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId()));
        }
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        $this->trans[$lang] = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $options = [])
    {
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        $val = isset($this->trans[$lang]) ? $this->trans[$lang] : null;

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(array $options = []): bool
    {
        return is_null($this->getValue($options));
    }
}
