<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Translation\LanguageCodes;

class TestType implements DataTypeInterface
{
    public function setValue($val, array $options = []): DataTypeInterface
    {
        return $this;
    }

    public function getValue(array $options = [])
    {
        return null;
    }

    public function getSerializedValue()
    {
        return null;
    }

    public function isNull(array $options = []): bool
    {
        return true;
    }

    public function getLanguages(): array
    {
        return [LanguageCodes::ANY];
    }

    public function setVersion(int $version): DataTypeInterface
    {
        return $this;
    }

    public function getVersion(): int
    {
        return 0;
    }

    public function isValidValue($val): bool
    {
        return true;
    }

    public function isEqualTo(DataTypeInterface $type): bool
    {
        return true;
    }

    public function isGreaterThan(DataTypeInterface $type): bool
    {
        return true;
    }

    public function canMergeWith(DataTypeInterface $type): bool
    {
        return true;
    }

    public function merge(DataTypeInterface $type): DataTypeInterface
    {
        return $this;
    }

    public function coerce($val)
    {
        return $this;
    }

    public function getSerializationId(): string
    {
        return 'TEST';
    }

    public function toArray(): array
    {
        return [];
    }

    public function fromArray(array $data): SerializableInterface
    {
        return $this;
    }

    public function setSerializer(Serializer $serializer): DataTypeInterface
    {
        return $this;
    }
}
