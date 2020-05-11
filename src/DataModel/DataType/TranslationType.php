<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class TranslationType extends AbstractDataType
{
    protected ?string $lang = null;
    protected ?string $val = null;

    public function __construct(?string $lang, ?string $val)
    {
        $this->lang = $lang;
        $this->val = $val;
    }

    /**
     * {@inheritdoc}
     */
    public function setLanguage(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
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
    public function merge(DataTypeInterface $type): self
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'TRANS';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'ver' => $this->getVersion(),
            'val' => $this->getValue(),
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
    public function isNull(array $options = []): bool
    {
        return is_null($this->val);
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        return [$this->lang];
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof TranslationType;
    }
}
