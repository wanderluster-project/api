<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;

class DatabaseTest extends FunctionalTest
{
    const TEST_DB = 'database_functional_test';

    public static function setUpBeforeClass(): void
    {
        $client = self::getCouchDbClient();
        if ($client->hasDB(self::TEST_DB)) {
            $client->deleteDB(self::TEST_DB);
        }
        $client->createDB(self::TEST_DB);
    }

    public function testCreateDocument(): void
    {
        $couchDB = $this->getCouchDbClient();
        $db = $couchDB->getDB(self::TEST_DB);
        $doc = $db->createDocument('test_save_document', ['foo' => 'bar']);
        $this->assertTrue($db->hasDocument('test_save_document'));
        $this->assertEquals('test_save_document', $doc->getId());
        $this->assertRegExp('/1-(.*)/', $doc->getRev());
        $db->deleteDocument($doc);
    }

    public function testGetDocument(): void
    {
        $couchDB = $this->getCouchDbClient();
        $db = $couchDB->getDB(self::TEST_DB);
        $this->assertFalse($db->hasDocument('test_get_document'));
        $db->createDocument('test_get_document', ['foo' => 'bar']);
        $this->assertTrue($db->hasDocument('test_get_document'));
        $doc = $db->getDocument('test_get_document');
        $this->assertEquals('test_get_document', $doc->getId());
        $this->assertRegExp('/1-(.*)/', $doc->getRev());
        $db->deleteDocument($doc);
    }

    public function testGetDocumentException(): void
    {
        $couchDB = $this->getCouchDbClient();
        $db = $couchDB->getDB(self::TEST_DB);
        try {
            $db->getDocument('test_get_document');
        } catch (WanderlusterException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    public function testUpdateDocument(): void
    {
        $couchDB = $this->getCouchDbClient();
        $db = $couchDB->getDB(self::TEST_DB);
        $doc = $db->createDocument('test_update_document', ['foo' => 'bar']);
        $this->assertRegExp('/1-(.*)/', $doc->getRev());
        $doc->setData(['foo' => 'baz']);
        $doc = $db->updateDocument($doc);
        $this->assertRegExp('/2-(.*)/', $doc->getRev());
    }
}
