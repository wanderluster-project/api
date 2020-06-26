<?php

declare(strict_types=1);

namespace App\Persistence\CouchDB;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Document
{
    protected string $id;
    protected ?string $rev;
    protected array $data = [];

    public function __construct(string $id, array $data, string $rev = null)
    {
        $this->id = $id;
        $this->rev = $rev;
        unset($data['_id']);
        unset($data['_rev']);
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRev(): string
    {
        return $this->rev;
    }

    public function setRev(string $rev): Document
    {
        return new Document($this->id, $this->data, $rev);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRawData(): array
    {
        $ret = $this->data;
        $ret['_id'] = $this->id;
        $ret['_rev'] = $this->rev;

        return $ret;
    }

    public function setData(array $data): Document
    {
        $newId = isset($data['_id']) ? $data['_id'] : null;
        $newRev = isset($data['_rev']) ? $data['_rev'] : null;
        unset($data['_id']);
        unset($data['_rev']);

        if ($this->id && $newId && $this->id !== $newId) {
            throw new WanderlusterException(sprintf(ErrorMessages::COUCHDB_ERROR, 'Document ID is immutable.'));
        }

        $id = $newId ? $newId : $this->id;
        $rev = $newRev ? $newRev : $this->rev;

        return new Document($id, $data, $rev);
    }
}
