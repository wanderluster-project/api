<?php

declare(strict_types=1);

namespace App\DataModel;

use App\DataModel\Validation\Constraints;

class Entity
{
    /**
     * @var Uuid
     */
    protected $uuid;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var Constraints
     */
    protected $constraints;

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getEntityType(): int
    {
        return $this->getEntityType();
    }

    public function getData(): Data
    {
        return $this->metadata->getSnapshot()->getData();
    }

    public function getVersion(): int
    {
        return $this->metadata->getSnapshot()->getVersion();
    }

    public function getConstraints(): Constraints
    {
        return $this->constraints;
    }
}
