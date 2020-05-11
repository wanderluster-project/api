<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class UrlType extends AbstractDataType
{
    protected ?string $val = null;

    /**
     * UrlType constructor.
     *
     * @param string|null $val
     *
     * @throws WanderlusterException
     */
    public function __construct($val = null, array $options = [])
    {
        $this->setValue($val);
        $ver = isset($options['ver']) ? (int) $options['ver'] : 0;
        $this->setVersion($ver);
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'URL';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'val' => $this->val,
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
        if (!is_string($val) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'String required'));
        }

        if (is_string($val)) {
            if (!filter_var($val, FILTER_VALIDATE_URL)) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'Invalid URL'));
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
        return [LanguageCodes::ANY];
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof UrlType;
    }
}
