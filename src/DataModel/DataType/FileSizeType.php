<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Exception;

class FileSizeType extends AbstractDataType
{
    const GB_BYTES = 1073741824;
    const MB_BYTES = 1048576;
    const KB_BYTES = 1024;

    protected ?int $val;

    /**
     * FileSizeType constructor.
     *
     * @param int|string|null $val
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
        return 'FILE_SIZE';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'val' => $this->getValue(),
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
    public function setValue($val, array $options = []): DataTypeInterface
    {
        if (is_string($val)) {
            try {
                $val = $this->parseFileSizeString($val);
            } catch (Exception $e) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'Invalid file size string'));
            }
        }

        if (!is_int($val) && !is_null($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATATYPE_VALUE, $this->getSerializationId(), 'Invalid file size'));
        }

        $this->val = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $options = [])
    {
        $formatted = isset($options['formatted']) ? true : false;
        if ($formatted) {
            return $this->formatSizeUnits($this->val);
        }

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
     * @param int|float $bytes
     *
     * @return string
     */
    protected function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / self::GB_BYTES, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / self::MB_BYTES, 2).' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / self::KB_BYTES, 2).' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif (1 == $bytes) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * @param string $str
     *
     * @return int
     *
     * @throws WanderlusterException
     */
    protected function parseFileSizeString($str)
    {
        $str = strtoupper(trim($str));
        $str = str_replace(',', '', $str);

        if (!preg_match('/([0-9.]+)\s*(GB|MB|KB|BYTES|BYTE)/', $str, $matches)) {
            throw new WanderlusterException();
        }

        $num = $matches[1];
        $unit = $matches[2];

        $bytes = 0;
        if ('GB' === $unit) {
            $bytes = self::GB_BYTES * $num;
        } elseif ('MB' === $unit) {
            $bytes = self::MB_BYTES * $num;
        } elseif ('KB' === $unit) {
            $bytes = self::KB_BYTES * $num;
        } elseif ('BYTES' === $unit) {
            $bytes = $num;
        } elseif ('BYTE' === $unit) {
            $bytes = $num;
        }

        return (int) floor($bytes);
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
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof FileSizeType;
    }
}
