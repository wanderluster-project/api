<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Snapshot;

use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;

class SnapshotTest extends FunctionalTest
{
    public function testConstructor(): void
    {
        // naked constructor
        $sut = new Snapshot($this->getSerializer());
        $this->assertEquals([], $sut->getLanguages());
    }

    public function testSetGetHas(): void
    {
        $sut = new Snapshot($this->getSerializer());

        // assert key does not exist
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));

        // set new value
        $sut->set('foo', 'bar', LanguageCodes::ENGLISH);
        $sut->set('sample_float', 3.14, LanguageCodes::ENGLISH);
        $sut->set('sample_boolean', false, LanguageCodes::ENGLISH);
        $sut->set('sample_datetime', new \DateTime('1/1/2010'), LanguageCodes::ENGLISH);
        try {
            // @phpstan-ignore-next-line
            $sut->set('sample_obj', new \stdClass(), LanguageCodes::ENGLISH);
            $this->fail('Exception not thown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Unable to determine the type for key - sample_obj.', $e->getMessage());
        }

        try {
            $sut->set('foo', 'bar', LanguageCodes::ANY);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        // confirm exists
        $this->assertEquals('bar', $sut->get('foo', LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut->get('foo', LanguageCodes::SPANISH));

        try {
            $sut->get('foo', LanguageCodes::ANY);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        $this->assertTrue($sut->has('foo', LanguageCodes::ANY));
        $this->assertTrue($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::SPANISH));
    }

    public function testDel(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));

        // set value
        $sut->set('foo', 150, LanguageCodes::ENGLISH);
        $this->assertTrue($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::SPANISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::ANY));

        // delete
        $sut->set('foo', null, LanguageCodes::ENGLISH);
        $sut->del('foo', LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::SPANISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));

        // set value
        $sut->set('foo', 'english version', LanguageCodes::ENGLISH);
        $sut->set('foo', 'spanish version', LanguageCodes::SPANISH);
        $sut->set('foo', 'french version', LanguageCodes::FRENCH);

        $this->assertTrue($sut->has('foo', LanguageCodes::ANY));
        $this->assertTrue($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::SPANISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::FRENCH));

        // delete english value only
        $sut->del('foo', LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::SPANISH));
        $this->assertTrue($sut->has('foo', LanguageCodes::FRENCH));
        $this->assertTrue($sut->has('foo', LanguageCodes::ANY));

        // delete any
        $sut->del('foo', LanguageCodes::ANY);
        $this->assertFalse($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::SPANISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::FRENCH));
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));
    }

    public function testKeys(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set('foo1', 150, LanguageCodes::ENGLISH);
        $sut->set('foo2', 100, LanguageCodes::ENGLISH);
        $sut->set('foo3', 250, LanguageCodes::SPANISH);
        $this->assertEquals(['foo1', 'foo2', 'foo3'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1', 'foo2', 'foo3'], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals(['foo1', 'foo2', 'foo3'], $sut->keys(LanguageCodes::ANY));

        // delete value
        $sut->del('foo1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo2', 'foo3'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo2', 'foo3'], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals(['foo2', 'foo3'], $sut->keys(LanguageCodes::ANY));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $sut->set('foo2', 'bar2', LanguageCodes::ENGLISH);
        $sut->set('foo3', 'bar3', LanguageCodes::SPANISH);
        $this->assertEquals(['foo1', 'foo2'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo3'], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals(['foo1', 'foo2', 'foo3'], $sut->keys(LanguageCodes::ANY));

        // delete value
        $sut->del('foo1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo2'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo3'], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals(['foo2', 'foo3'], $sut->keys(LanguageCodes::ANY));
    }

    public function testAll(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set('foo', 150, LanguageCodes::ENGLISH);
        $this->assertEquals(['foo' => 150], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo' => 150], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals(['foo' => 150], $sut->all(LanguageCodes::FRENCH));

        // delete value
        $sut->del('foo', LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([], $sut->all(LanguageCodes::FRENCH));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getSerializer());
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));

        // set values
        $sut->set('foo', 'english value', LanguageCodes::ENGLISH);
        $sut->set('foo', 'spanish value', LanguageCodes::SPANISH);
        $sut->set('foo', 'french value', LanguageCodes::FRENCH);
        $this->assertEquals(['foo' => 'english value'], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo' => 'spanish value'], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals(['foo' => 'french value'], $sut->all(LanguageCodes::FRENCH));

        // confirm exception
        try {
            $sut->all(LanguageCodes::ANY);
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        // delete
        $sut->del('foo', LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo' => 'spanish value'], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals(['foo' => 'french value'], $sut->all(LanguageCodes::FRENCH));

        $sut->del('foo', LanguageCodes::ANY);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([], $sut->all(LanguageCodes::FRENCH));
    }

    public function testDecodeSnapshot(): void
    {
        $json = '{"type":"SNAPSHOT","version":10,"data":{"foo1":{"type":"LOCALIZED_STRING","val":[{"type":"TRANS","val":"bar1","ver":0,"lang":"en"},{"type":"TRANS","val":"bar2","ver":0,"lang":"es"}]}}}';
        $sut = $this->getSerializer()->decode($json);
        $this->assertEquals(10, $sut->getVersion());
        // @phpstan-ignore-next-line
        $this->assertTrue($sut->has('foo1', LanguageCodes::ANY));
    }

    public function testFromArrayExceptions(): void
    {
        $sut = new Snapshot($this->getSerializer());

        // missing type
        try {
            $sut->fromArray([
                'version' => 10, 'data' => [],
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Missing Field: type.', $e->getMessage());
        }

        // missing version
        try {
            $sut->fromArray([
               'type' => 'SNAPSHOT',  'data' => [],
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Missing Field: version.', $e->getMessage());
        }

        // missing data
        try {
            $sut->fromArray([
                'type' => 'SNAPSHOT',  'version' => 10,
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Missing Field: data.', $e->getMessage());
        }

        // invalid type
        try {
            $sut->fromArray([
                'type' => 'FOO',  'version' => 10, 'data' => [],
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Invalid Type: FOO.', $e->getMessage());
        }
    }
}
