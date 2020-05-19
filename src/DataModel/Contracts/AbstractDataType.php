<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

abstract class AbstractDataType implements DataTypeInterface
{
    protected int $ver = 0;

    /**
     * @var mixed
     */
    protected $val;

    /**
     * AbstractDataType constructor.
     *
     * @param mixed $val
     *
     * @throws WanderlusterException
     */
    public function __construct($val = null, array $options = [])
    {
        if (!is_null($val)) {
            $this->setValue($val, $options);
        }
        $ver = isset($options['ver']) ? (int) $options['ver'] : 0;
        if ($ver) {
            $this->setVersion($ver);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(int $version): self
    {
        if ($version < 0) {
            throw new WanderlusterException(sprintf(ErrorMessages::VERSION_INVALID, $version));
        }
        $this->ver = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): int
    {
        return $this->ver;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): DataTypeInterface
    {
        $val = $this->coerce($val);
        if (!$this->isValidValue($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
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
    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'val' => $this->getSerializedValue(),
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
        $val = $this->coerce($data['val']);
        $ver = (int) $data['ver'];
        $lang = isset($data['lang']) ? $data['lang'] : null;

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }

        $options = [];
        if ($lang) {
            $options['lang'] = $lang;
        }

        $this->setValue($val, $options);
        $this->setVersion($ver);

        return $this;
    }

    public function isValidValue($val): bool
    {
        try {
            $this->coerce($val);
        } catch (WanderlusterException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(DataTypeInterface $type): bool
    {
        if (!$this->canMergeWith($type)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_COMPARISON_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        return $this->getSerializedValue() === $type->getSerializedValue();
    }

    /**
     * {@inheritdoc}
     */
    public function isGreaterThan(DataTypeInterface $type): bool
    {
        if (!$this->canMergeWith($type)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_COMPARISON_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        return $this->getValue() > $type->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function merge(DataTypeInterface $type): self
    {
        if (!$this->canMergeWith($type)) {
            throw new WanderlusterException(sprintf(ErrorMessages::MERGE_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        $thisVal = $this->getValue();
        $thatVal = $type->getValue();
        $thisVer = $this->getVersion();
        $thatVer = $type->getVersion();

        // previous version... do nothing
        if ($thatVer < $thatVer) {
            return $this;
        }

        // greater version, use its value
        if ($thatVer > $thisVer) {
            $this->setVersion($thatVer);
            $this->setValue($thatVal);

            return $this;
        }

        // handle merge conflict
        if ($thatVer === $thisVer && $thisVal !== $thatVal) {
            if ($type->isGreaterThan($this)) {
                $this->setValue($thatVal);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(array $options = []): bool
    {
        if (is_array($this->val)) {
            return 0 === count($this->val);
        }

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
    public function getSerializedValue()
    {
        return $this->val;
    }
}
