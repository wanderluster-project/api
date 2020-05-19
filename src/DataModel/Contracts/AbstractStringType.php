<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

abstract class AbstractStringType extends AbstractDataType
{
    /**
     * Only URL formatted strings allowed.
     * {@inheritdoc}
     */
    public function isValidValue($val): bool
    {
        try {
            $val = $this->coerce($val);
        } catch (WanderlusterException $e) {
            return false;
        }

        if (is_null($val)) {
            return true;
        }

        return is_string($val);
    }

    /**
     * {@inheritdoc}
     */
    public function coerce($val)
    {
        if (is_null($val) || (is_string($val) && $this->isValidPattern($val))) {
            return $val;
        }

        if (is_bool($val)) {
            if (true === $val) {
                return 'TRUE';
            } else {
                return 'FALSE';
            }
        }

        try {
            $val = (string) $val;
        } catch (\Error $e) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        if (!$this->isValidPattern($val)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_DATA_TYPE_VALUE, $this->getSerializationId()));
        }

        return $val;
    }

    abstract public function isValidPattern(string $val): bool;
}
