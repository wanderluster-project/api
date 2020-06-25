<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Exception\WanderlusterException;

class Database
{
    protected string $name;
    protected Client $client;

    /**
     * Database constructor.
     */
    public function __construct(string $name, Client $client)
    {
        $this->name = $name;
        $this->client = $client;
    }

    /**
     * @throws WanderlusterException
     */
    public function saveDocument(string $id, array $doc): array
    {
        return $this->client->put('/'.$this->name.'/'.$id, $doc);
    }

    /**
     * @throws WanderlusterException
     */
    public function getDocument(string $id): array
    {
        return $this->client->get('/'.$this->name.'/'.$id);
    }
}
