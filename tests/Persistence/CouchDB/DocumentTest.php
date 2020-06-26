<?php

declare(strict_types=1);

namespace App\Tests\Persistence\CouchDB;

use App\Exception\WanderlusterException;
use App\Persistence\CouchDB\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testConstructor(): void
    {
        $doc = new Document('document_test_constructor', ['foo' => 'bar'], '1-foobar');
        $this->assertEquals('document_test_constructor', $doc->getId());
        $this->assertEquals(['foo' => 'bar'], $doc->getData());
        $this->assertEquals(['foo' => 'bar', '_id' => 'document_test_constructor', '_rev' => '1-foobar'], $doc->getRawData());
    }

    public function testSetRev(): void
    {
        $doc = new Document('document_test_constructor', ['foo' => 'bar'], '1-foobar');
        $this->assertEquals('1-foobar', $doc->getRev());
        $doc = $doc->setRev('2-foobar');
        $this->assertEquals('2-foobar', $doc->getRev());
    }

    public function testSetData(): void
    {
        $doc = new Document('document_test_constructor', ['foo' => 'bar'], '1-foobar');
        $this->assertEquals(['foo' => 'bar'], $doc->getData());
        $doc = $doc->setData(['baz' => 'ban', '_rev' => '2-foobar']);
        $this->assertEquals(['baz' => 'ban'], $doc->getData());
        $this->assertEquals(['baz' => 'ban', '_id' => 'document_test_constructor', '_rev' => '2-foobar'], $doc->getRawData());
    }

    public function testSetDataException(): void
    {
        $doc = new Document('document_test_constructor', [], '1-foobar');
        try {
            $doc->setData(['_id' => 'foo']);
        } catch (WanderlusterException $e) {
            $this->assertEquals('CouchDB error - Document ID is immutable.', $e->getMessage());
        }
    }
}
