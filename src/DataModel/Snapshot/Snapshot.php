<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\DateTimeType;
use App\DataModel\DataType\IntegerType;
use App\DataModel\DataType\NumericType;
use App\DataModel\DataType\String\LocalizedStringType;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\Security\User;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class Snapshot implements SerializableInterface
{
    const SERIALIZATION_ID = 'SNAPSHOT';

    protected ?int $version = null;
    protected ?DateTimeImmutable $createdAt = null;
    protected ?User $createdBy = null;
    protected Serializer $serializer;

    /**
     * @var DataTypeInterface[]|null[]
     */
    protected array $data = [];

    /**
     * Set the value of an attribute.
     *
     * @param bool|int|float|string|DateTime|DateTimeImmutable|DataTypeInterface|null $value
     *
     * @throws WanderlusterException
     */
    public function set(string $key, $value, string $lang): void
    {
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        if (is_null($value)) {
            $this->del($key, $lang);
        }

        $key = (string) $key;
        $typedVal = null;

        if ($this->has($key, LanguageCodes::ANY)) {
            $typedVal = $this->data[$key];
        } else {
            if (is_bool($value)) {
                $typedVal = new BooleanType($value);
            } elseif (is_int($value)) {
                $typedVal = new IntegerType($value);
            } elseif (is_float($value)) {
                $typedVal = new NumericType($value);
            } elseif (is_object($value)) {
                if ($value instanceof DateTimeInterface) {
                    $typedVal = new DateTimeType($value);
                }
            } elseif (is_string($value)) {
                $typedVal = new LocalizedStringType();
            } else {
                throw new WanderlusterException(sprintf(ErrorMessages::UNAGLE_DETERMINE_TYPE, $key));
            }
        }

        $typedVal->setValue($value, ['lang' => $lang]);
        $this->data[$key] = $typedVal;
        ksort($this->data);
    }

    /**
     * Get the value of an attribute.
     *
     * @return string|null
     *
     * @throws WanderlusterException
     */
    public function get(string $key, string $lang)
    {
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        if (!$this->has($key, $lang)) {
            return null;
        }

        return $this->data[$key]->getValue(['lang' => $lang]);
    }

    public function del(string $key, string $lang): void
    {
        if (LanguageCodes::ANY === $lang) {
            $this->data[$key] = null;

            return;
        }

        if (is_null($this->data[$key])) {
            return;
        }

        $val = $this->data[$key];

        // Type matches all languages (ex: Integer) so just delete
        if ($val->getLanguages() === [LanguageCodes::ANY]) {
            $this->data[$key] = null;
        } elseif ($val->getLanguages() === [$lang]) {
            $this->data[$key] = null;
        } else {
            $val->setValue(null, ['lang' => $lang]);
        }
    }

    /**
     * Check if attribute exists.  Return true if exists, false otherwise.
     */
    public function has(string $key, string $lang): bool
    {
        if (!array_key_exists($key, $this->data)) {
            return false;
        }

        $value = $this->data[$key];

        if (is_null($value)) {
            return false;
        }

        if (LanguageCodes::ANY === $lang) {
            return true;
        }

        if (is_null($value->getValue(['lang' => $lang]))) {
            return false;
        }

        return true;
    }

    /**
     * Return the keys as an array.
     * Filters out any NULL values.
     *
     * @return string[]
     *
     * @throws WanderlusterException
     */
    public function keys(string $lang): array
    {
        $return = [];

        if (LanguageCodes::ANY === $lang) {
            foreach ($this->data as $key => $item) {
                if (is_null($item)) {
                    continue;
                }
                $return[] = $key;
            }
        } else {
            foreach ($this->data as $key => $item) {
                if (is_null($item)) {
                    continue;
                }
                if (!is_null($item->getValue(['lang' => $lang]))) {
                    $return[] = $key;
                }
            }
        }

        ksort($return);

        return $return;
    }

    /**
     * Return all the key=>value pairs.
     * Filters out any NULL values.
     *
     * @return mixed[]
     *
     * @throws WanderlusterException
     */
    public function all(string $lang): array
    {
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        $return = [];
        foreach ($this->data as $key => $item) {
            if (!is_null($item)) {
                if (!$item->isNull(['lang' => $lang])) {
                    $return[$key] = $item->getValue(['lang' => $lang]);
                }
            }
        }
        ksort($return);

        return $return;
    }

    public function getLanguages(): array
    {
        $ret = [];
        foreach ($this->data as $key => $value) {
            if (!is_null($value)) {
                $ret = array_merge($ret, $value->getLanguages());
            }
        }
        $ret = array_unique($ret);
        ksort($ret);

        return $ret;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->data as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $value->toArray();
            }
        }

        return [
            'type' => $this->getSerializationId(),
            'version' => $this->version,
            'data' => $data,
        ];
    }

    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'version', 'data'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $version = (int) $data['version'];
        $data = $data['data'];

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }

        if ($version) {
            $this->version = $version;
        }

        foreach ($data as $key => $typeData) {
            $type = $typeData['type'];
            $typeObj = $this->serializer->instantiate($type, $typeData);
            $this->data[$key] = $typeObj;
        }

        return $this;
    }

    public function getSerializationId(): string
    {
        return self::SERIALIZATION_ID;
    }

    public function setSerializer(Serializer $serializer): SerializableInterface
    {
        $this->serializer = $serializer;

        return $this;
    }
}
