<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Persistence\CouchDB\Database;
use App\Tests\FunctionalTest;
use Exception;

class ClientTest extends FunctionalTest
{
    const TEST_DB_1 = 'client_functional_test_1';
    const TEST_DB_2 = 'client_functional_test_2';

    public static function setUpBeforeClass(): void
    {
        $client = self::getCouchClient();
        if ($client->hasDB(self::TEST_DB_1)) {
            $client->deleteDB(self::TEST_DB_1);
        }
        if ($client->hasDB(self::TEST_DB_2)) {
            $client->deleteDB(self::TEST_DB_2);
        }
    }

    public function testIsAvailable(): void
    {
        $client = $this->getCouchClient();
        $this->assertTrue($client->isAvailable());
    }

    public function testCreateGetDeleteDatabase(): void
    {
        $client = self::getCouchClient();

        // test creating
        $this->assertFalse($client->hasDB(self::TEST_DB_1));
        $db = $client->createDB(self::TEST_DB_1);
        $this->assertInstanceOf(Database::class, $db);
        $this->assertTrue($client->hasDB(self::TEST_DB_1));

        // test getting
        $db = $client->getDB(self::TEST_DB_1);
        $this->assertInstanceOf(Database::class, $db);

        // test deleting
        $client->deleteDB(self::TEST_DB_1);
        $this->assertFalse($client->hasDB(self::TEST_DB_1));
    }

    public function testExceptionOnCreateDB(): void
    {
        $client = $this->getCouchClient();

        // initially create database
        $client->createDB(self::TEST_DB_2);
        $this->assertTrue($client->hasDB(self::TEST_DB_2));

        // exception should be thrown if database already exists
        try {
            $client->createDB(self::TEST_DB_2);
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('{"error":"file_exists","reason":"The database could not be created, the file already exists."}', trim($e->getMessage()));
        }

        // cleanup database
        $client->deleteDB(self::TEST_DB_2);
        $this->assertFalse($client->hasDB(self::TEST_DB_2));
    }
}
