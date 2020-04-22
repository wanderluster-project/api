<?php

declare(strict_types=1);

namespace App\RDF;

use DateTimeImmutable;

class Snapshot
{
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

    /**
     * @var int
     */
    protected $version;

    public function getData(): Data
    {
        return $this->data;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
