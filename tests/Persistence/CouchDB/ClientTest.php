<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Persistence\CouchDB\Database;
use App\Tests\FunctionalTest;

class ClientTest extends FunctionalTest
{
    public function testIsAvailable(): void
    {
        $client = $this->getCouchClient();
        $this->assertTrue($client->isAvailable());
    }

    public function testGetDatabase(): void
    {
        $client = $this->getCouchClient();
        $db = $client->getDB('entity');
        $this->assertInstanceOf(Database::class, $db);
    }

    public function testCreateDatabase(): void
    {
        $client = $this->getCouchClient();
        if ($client->hasDB('entity')) {
            $client->deleteDB('entity');
        }
        $db = $client->createDB('entity');
        $this->assertInstanceOf(Database::class, $db);
    }
}
