<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Serializer\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class StringType implements TypeInterface
{
    /**
     * Associative array identifying languageCode => translation.
     *
     * @var string[]|null
     */
    protected $trans;

    /**
     * @var int
     */
    protected $ver = 0;

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
    public function getTypeId(): string
    {
        return 'STRING';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getTypeId(),
            'trans' => $this->trans,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'trans'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $trans = $data['trans'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }

        if (!is_array($trans)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'trans should be an array'));
        }

        foreach ($trans as $lang => $val) {
            $this->setValue($val, ['lang' => $lang]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($val, array $options = []): TypeInterface
    {
        if (!is_string($val) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'String required'));
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
    public function setVersion(int $version): TypeInterface
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
}
