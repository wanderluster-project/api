<?php

declare(strict_types=1);

namespace App\DataModel\DataType\String;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\AbstractStringType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class StringType extends AbstractStringType
{
    const SERIALIZATION_ID = 'STRING';

    /**
     * @var TranslationType[]
     */
    protected $val = [];

    public function __construct()
    {
        parent::__construct();
    }

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
        $val = $this->coerce($val);
        $lang = isset($options['lang']) ? $options['lang'] : null;
        $ver = isset($options['ver']) ? $options['ver'] : 0;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }
        $this->setTranslation($lang, $val, $ver);

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

        return $this->getTranslation($lang);
    }

    /**
     * Get the string translation for a given language.
     *
     * @throws WanderlusterException
     */
    public function getTranslation(string $lang): ?string
    {
        if (isset($this->val[$lang])) {
            $trans = $this->val[$lang];

            return $trans->getValue();
        }

        return null;
    }

    /**
     * Set the string translation for a given language.
     *
     * @param string $val
     *
     * @throws WanderlusterException
     */
    public function setTranslation(string $lang, ?string $val, int $ver = null): self
    {
        if (isset($this->val[$lang])) {
            $trans = $this->val[$lang];
            /*
             * @var TranslationType $trans
             */
            $trans->setValue($val);
            if ($ver) {
                $trans->setVersion($ver);
            }
        } else {
            $trans = new TranslationType($val, ['ver' => $ver, 'lang' => $lang]);
        }

        $this->val[$lang] = $trans;
        ksort($this->val);

        return $this;
    }

    public function getLanguages(): array
    {
        $ret = array_keys($this->val);
        sort($ret);

        return $ret;
    }

    public function toArray(): array
    {
        $ret = parent::toArray();
        unset($ret['ver']);

        return $ret;
    }

    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'val'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $translations = $data['val'];

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }
        if (!is_array($translations)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'field val must be an array'));
        }

        foreach ($translations as $data) {
            $translation = new TranslationType();
            $translation->fromArray($data);
            $this->val[$translation->getLanguage()] = $translation;
        }

        return $this;
    }

    public function merge(DataTypeInterface $type): AbstractDataType
    {
        if (!$this->canMergeWith($type)) {
            throw new WanderlusterException(sprintf(ErrorMessages::MERGE_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        $reflection = new \ReflectionClass(StringType::class);
        $prop = $reflection->getProperty('val');
        $prop->setAccessible(true);
        $thatTranslations = $prop->getValue($type);

        /*
         * @var StringType $type
         */
        foreach ($thatTranslations as $lang => $item) {
            if (!isset($this->val[$lang])) {
                $this->val[$lang] = new TranslationType();
            }
            $this->val[$lang]->merge($item);
            $this->val[$lang]->setLanguage($lang);
        }

        return $this;
    }

    public function isGreaterThan(DataTypeInterface $type): bool
    {
        throw new WanderlusterException(sprintf(ErrorMessages::UNABLE_TO_COMPARE, $this->getSerializationId()));
    }

    public function isEqualTo(DataTypeInterface $type): bool
    {
        throw new WanderlusterException(sprintf(ErrorMessages::UNABLE_TO_COMPARE, $this->getSerializationId()));
    }

    public function getSerializedValue()
    {
        $ret = [];
        foreach ($this->val as $lang => $trans) {
            $ret[] = $trans->toArray();
        }

        return $ret;
    }

    public function isNull(array $options = []): bool
    {
        $lang = isset($options['lang']) ? $options['lang'] : null;
        if (!$lang) {
            throw new WanderlusterException(sprintf(ErrorMessages::OPTION_REQUIRED, 'lang'));
        }
        if (LanguageCodes::ANY === $lang) {
            throw new WanderlusterException(ErrorMessages::UNABLE_TO_USE_ANY_LANGUAGE);
        }

        if (!isset($this->val[$lang])) {
            return true;
        }

        return $this->val[$lang]->isNull();
    }

    public function setVersion(int $version): self
    {
        throw new WanderlusterException(sprintf(ErrorMessages::UNABLE_TO_SET_VERSION, $this->getSerializationId()));
    }

    public function isValidPattern(string $val): bool
    {
        return true;
    }

    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof StringType;
    }
}
