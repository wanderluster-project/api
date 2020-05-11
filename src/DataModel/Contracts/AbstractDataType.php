<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

abstract class AbstractDataType implements DataTypeInterface
{
    protected int $ver = 0;

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
    public function isEqual(DataTypeInterface $type): bool
    {
        if (!$this->canMergeWith($type)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_COMPARISON_UNSUCCESSFUL, $type->getSerializationId(), $this->getSerializationId()));
        }

        return null === $type->getValue();
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
}
