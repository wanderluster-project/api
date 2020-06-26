<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Exception\ErrorMessages;
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
    public function createDocument(string $id, array $data): Document
    {
        $data = $this->client->put('/'.$this->name.'/'.$id, $data);
        if (!isset($data['ok']) || !isset($data['rev']) || true === !$data['ok']) {
            throw new WanderlusterException(sprintf(ErrorMessages::COUCHDB_ERROR, 'Error creating document - '.$id));
        }
        $rev = $data['rev'];

        return new Document($id, $data, $rev);
    }

    /**
     * @throws WanderlusterException
     */
    public function getDocument(string $id): Document
    {
        $data = $this->client->get('/'.$this->name.'/'.$id);
        if (!isset($data['_id']) || !isset($data['_rev'])) {
            throw new WanderlusterException(sprintf(ErrorMessages::COUCHDB_ERROR, 'Error getting document - '.$id));
        }

        return new Document($id, $data, $data['_rev']);
    }

    public function updateDocument(Document $doc): Document
    {
        $id = $doc->getId();
        $data = $this->client->put('/'.$this->name.'/'.$doc->getId(), $doc->getRawData());
        if (!isset($data['ok']) || !isset($data['rev']) || true === !$data['ok']) {
            throw new WanderlusterException(sprintf(ErrorMessages::COUCHDB_ERROR, 'Error updating document - '.$id));
        }

        return new Document($id, $data, $data['rev']);
    }

    public function deleteDocument(Document $doc): array
    {
        return $this->client->delete('/'.$this->name.'/'.$doc->getId().'?rev='.$doc->getRev());
    }

    public function hasDocument(string $id): bool
    {
        try {
            $this->getDocument($id);

            return true;
        } catch (WanderlusterException $e) {
            return false;
        }
    }
}
