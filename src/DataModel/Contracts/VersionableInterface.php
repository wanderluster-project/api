<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

use App\Exception\WanderlusterException;

interface VersionableInterface
{
    /**
     * Set the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function setVersion(int $version): self;

    /**
     * Get the version for the data type.
     *
     * @throws WanderlusterException
     */
    public function getVersion(): int;
}
