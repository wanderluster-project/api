<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class StringType extends AbstractDataType
{
    /**
     * Associative array identifying languageCode => translation.
     *
     * @var string[]|null
     */
    protected $trans;

    /**
     * Boolean constructor.
     *
     * @param string[] $trans
     *
     * @throws WanderlusterException
     */
    public function __construct(array $trans = [], array $options = [])
    {
        foreach ($trans as $lang => $val) {
            $this->setValue($val, ['lang' => $lang]);
        }
        $ver = isset($options['ver']) ? (int) $options['ver'] : 0;
        $this->setVersion($ver);
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return 'STRING';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'val' => $this->trans,
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

        if (!is_array($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'val should be an array'));
        }

        foreach ($val as $lang => $item) {
            $this->setValue($item, ['lang' => $lang]);
        }
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
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }
        $this->trans[$lang] = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $options = [])
    {
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }
        $val = isset($this->trans[$lang]) ? $this->trans[$lang] : null;

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(array $options = []): bool
    {
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        return is_null($this->getValue($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        $langauges = array_keys($this->trans);
        sort($langauges);

        return $langauges;
    }

    public function merge(DataTypeInterface $type): self
    {
        if (!$type instanceof StringType) {
            throw new WanderlusterException(sprintf(ErrorMessages::MERGE_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        $languages = array_unique(array_merge($this->getLanguages(), $type->getLanguages()));
        sort($languages);

        $thisVal = [];
        foreach ($languages as $lang) {
            $thisVal[$lang] = $this->getValue(['lang' => $lang]);
        }
        $thatVal = [];
        foreach ($languages as $lang) {
            $thatVal[$lang] = $type->getValue(['lang' => $lang]);
        }

        $thisVer = $this->getVersion();
        $thatVer = $type->getVersion();

        // previous version... do nothing
        if ($thatVer < $thatVer) {
            return $this;
        }

        // greater version, use its value
        if ($thatVer > $thisVer) {
            foreach ($thatVal as $lang => $translation) {
                $this->setValue($translation, ['lang' => $lang]);
            }
            $this->setVersion($thatVer);

            return $this;
        }

        // handle merge conflict
        if ($thatVer === $thisVer) {
            foreach ($languages as $lang) {
                if ($thatVal[$lang] > $thisVal[$lang]) {
                    $this->setValue($thatVal[$lang], ['lang' => $lang]);
                }
            }
        }

        return $this;
    }

    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof StringType;
    }
}
