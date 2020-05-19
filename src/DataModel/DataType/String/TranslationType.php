<?php

declare(strict_types=1);

namespace App\DataModel\DataType\String;

use App\DataModel\Contracts\AbstractStringType;
use App\DataModel\Contracts\DataTypeInterface;

class TranslationType extends AbstractStringType
{
    const SERIALIZATION_ID = 'TRANS';

    protected ?string $lang = null;

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return self::SERIALIZATION_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if ($lang) {
            $this->lang = $lang;
        }
        parent::setValue($val, $options);

        return $this;
    }

    public function setLanguage(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage(): ?string
    {
        return $this->lang;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        if ($this->lang) {
            return [$this->lang];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $ret = parent::toArray();
        $ret['lang'] = $this->lang;

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof TranslationType;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidPattern(string $val): bool
    {
        return true;
    }
}
