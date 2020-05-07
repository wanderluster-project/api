<?php

declare(strict_types=1);

namespace App\DataModel\Types;

use App\DataModel\Serializer\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class MimeType implements TypeInterface
{
    const PATTERN = '/^[-\w]+\/[-\w\.\+]+$/';

    /**
     * @var string|null
     */
    protected $val;

    /**
     * @var int
     */
    protected $ver = 0;

    /**
     * MimeType constructor.
     *
     * @param string|null $val
     *
     * @throws WanderlusterException
     */
    public function __construct($val = null, array $options = [])
    {
        $this->setValue($val, $options);

        $ver = isset($options['ver']) ? (int) $options['ver'] : 0;
        $this->setVersion($ver);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId(): string
    {
        return 'MIME_TYPE';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getTypeId(),
            'val' => $this->val,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'val'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $val = $data['val'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }
        $this->setValue($val);

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

        if (is_string($val)) {
            if (!preg_match(self::PATTERN, $val)) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getTypeId(), 'Invalid MimeType'));
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
        return is_null($this->getValue($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages(): array
    {
        return [LanguageCodes::ANY];
    }
}
