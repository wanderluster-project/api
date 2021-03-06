<?php

declare(strict_types=1);

namespace App\DataModel\DataType;

use App\DataModel\Contracts\AbstractDataType;
use App\DataModel\Contracts\DataTypeInterface;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class FileSizeType extends AbstractDataType
{
    const SERIALIZATION_ID = 'FILE_SIZE';
    const GB_BYTES = 1073741824;
    const MB_BYTES = 1048576;
    const KB_BYTES = 1024;

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
    public function canMergeWith(DataTypeInterface $type): bool
    {
        return $type instanceof FileSizeType;
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (is_null($val) || is_int($val)) {
            return $val;
        }

        if (is_string($val)) {
            return $this->parseFileSizeString($val);
        }

        throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
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
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
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
}
