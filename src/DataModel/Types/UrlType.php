<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Contracts\TypeInterface;
use App\DataModel\Contracts\VersionableTrait;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class UrlType implements TypeInterface
{
    use VersionableTrait;
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
    public function setValue($val, array $options = []): TypeInterface
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

    public function merge(TypeInterface $type): void
    {
        if (!$type instanceof UrlType) {
            throw new WanderlusterException(sprintf(ErrorMessages::MERGE_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        $thisVal = $this->getValue();
        $thatVal = $type->getValue();
        $thisVer = $this->getVersion();
        $thatVer = $type->getVersion();

        // previous version... do nothing
        if ($thatVer < $thatVer) {
            return;
        }

        // greater version, use its value
        if ($thatVer > $thisVer) {
            $this->setVersion($thatVer);
            $this->setValue($thatVal);

            return;
        }

        // handle merge conflict
        if ($thatVer === $thisVer && $thisVal !== $thatVal) {
            if ($thatVal > $thisVal) {
                $this->setValue($thatVal);
            }
        }
    }
}
