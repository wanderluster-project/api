<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Serializer\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\DataModel\Types\BooleanType;
use App\DataModel\Types\DateTimeType;
use App\DataModel\Types\IntegerType;
use App\DataModel\Types\NumericType;
use App\DataModel\Types\StringType;
use App\DataModel\Types\TypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use App\Security\User;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class Snapshot implements SerializableInterface
{
    /**
     * @var SnapshotId|null
     */
    protected $snapshotId = null;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt = null;

    /**
     * @var User
     */
    protected $createdBy = null;

    /**
     * @var TypeInterface[]|null[]
     */
    protected $data = [];

    /**
     * Set the value of an attribute.
     *
     * @param string                                                              $key
     * @param bool|int|float|string|DateTime|DateTimeImmutable|TypeInterface|null $value
     * @param string                                                              $lang
     *
     * @throws WanderlusterException
     */
    public function set($key, $value, $lang): void
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
                $typedVal = new StringType();
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
     * @param string $key
     * @param string $lang
     *
     * @return string|null
     *
     * @throws WanderlusterException
     */
    public function get($key, $lang)
    {
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        if (!$this->has($key, $lang)) {
            return null;
        }

        return $this->data[$key]->getValue(['lang' => $lang]);
    }

    /**
     * @param string $key
     * @param string $lang
     */
    public function del($key, $lang): void
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
     *
     * @param string $key
     * @param string $lang
     */
    public function has($key, $lang): bool
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
     * @param string $lang
     *
     * @return string[]
     *
     * @throws WanderlusterException
     */
    public function keys($lang): array
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
     * @param string $lang
     *
     * @return mixed[]
     *
     * @throws WanderlusterException
     */
    public function all($lang): array
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

    public function toArray(): array
    {
        $snapshotId = (string) $this->snapshotId;
        $data = [];
        foreach ($this->data as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $value->toArray();
            }
        }

        return [
            'type' => $this->getTypeId(),
            'snapshot_id' => $snapshotId ? $snapshotId : null,
            'data' => $data,
        ];
    }

    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'snapshot_id', 'data'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $snapshotId = $data['snapshot_id'];
        $data = $data['data'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }

        if ($snapshotId) {
            $this->snapshotId = new SnapshotId($snapshotId);
        }

        foreach ($data as $key => $typeData) {
            $type = $typeData['type'];
            $typeObj = null;

            switch ($type) {
                case 'STRING':
                    $typeObj = new StringType();
                    $typeObj->fromArray($typeData);
                    break;
                case 'INT':
                    $typeObj = new IntegerType();
                    $typeObj->fromArray($typeData);
                    break;
                default:
                    // @todo move this logic to the serializer
                    throw new \Exception('@todo - '.$type);
            }
            $this->data[$key] = $typeObj;
        }

        return $this;
    }

    public function getTypeId(): string
    {
        return 'SNAPSHOT';
    }
}
