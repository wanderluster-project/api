<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;

class TranslationType extends AbstractDataType
{
    protected ?string $lang = null;

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

    /**
     * Only strings allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        if (is_null($val)) {
            return true;
        }

        return is_string($val);
    }
}
