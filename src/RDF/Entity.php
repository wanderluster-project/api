<?php

declare(strict_types=1);

namespace App\RDF;

use App\Sharding\Uuid;
use DateTimeImmutable;

class Entity
{
    /**
     * @var Uuid
     */
    protected $uuid;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var User
     */
    protected $createdBy;

    /**
     * @var Data
     */
    protected $data;

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }
}
