<?php

namespace App\RDF;

class Entity
{
    protected $uuid;
    protected $createdAt;
    protected $createdBy;
    protected $data;

    public function getUuid()
    {
        return $this->uuid;
    }
}
