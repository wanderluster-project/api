<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Tests\FunctionalTest;

class DatabaseTest extends FunctionalTest
{
    public function testSaveDocument(): void
    {
        $couchDB = $this->getCouchClient();
        $db = $couchDB->getDB('entity');
        $data = $db->saveDocument('test', ['foo' => 'bar']);
        $this->assertEquals(true, $data['ok']);
        $this->assertEquals('test', $data['id']);
    }

    public function testGetDocument(): void
    {
        $couchDB = $this->getCouchClient();
        $db = $couchDB->getDB('entity');
        $data = $db->getDocument('test');
        $this->assertEquals('test', $data['_id']);
        $this->assertEquals('bar', $data['foo']);
    }
}
