<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

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
    public function getSerializationId(): string
    {
        return 'TRANS';
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

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (!$this->isValidValue($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId()));
        }

        if (is_null($val)) {
            return $val;
        }

        return (string) $val;
    }
}
