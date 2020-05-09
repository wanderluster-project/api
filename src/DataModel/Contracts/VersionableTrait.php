<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

trait VersionableTrait
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
}
