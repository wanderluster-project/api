<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Snapshot;

use App\DataModel\Attributes\Attributes;
use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;

class SnapshotTest extends FunctionalTest
{
    public function testConstructor(): void
    {
        // naked constructor
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertEquals([], $sut->getLanguages());
    }

    public function testSetGetHas(): void
    {
        $sut = new Snapshot($this->getAttributeMangager());

        // assert key does not exist
        $this->assertFalse($sut->has('foo', LanguageCodes::ANY));

        // set new value
        $sut->set(Attributes::CORE_TEST_STRING, 'bar', LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_NUM, 3.14, LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_BOOLEAN, false, LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_DATE_TIME, new \DateTime('1/1/2010'), LanguageCodes::ENGLISH);

        try {
            $sut->set(Attributes::CORE_TEST_STRING, 'bar', LanguageCodes::ANY);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        // confirm exists
        $this->assertEquals('bar', $sut->get(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut->get(Attributes::CORE_TEST_STRING, LanguageCodes::SPANISH));

        try {
            $sut->get(Attributes::CORE_TEST_STRING, LanguageCodes::ANY);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ANY));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::SPANISH));
    }

    public function testDel(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertFalse($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::ANY));

        // set value
        $sut->set(Attributes::CORE_TEST_INT, 150, LanguageCodes::ENGLISH);
        $this->assertTrue($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::SPANISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::ANY));

        // delete
        $sut->set(Attributes::CORE_TEST_INT, null, LanguageCodes::ENGLISH);
        $sut->del(Attributes::CORE_TEST_INT, LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::SPANISH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_INT, LanguageCodes::ANY));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ANY));

        // set value
        $sut->set(Attributes::CORE_TEST_STRING, 'english version', LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'spanish version', LanguageCodes::SPANISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'french version', LanguageCodes::FRENCH);

        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ANY));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::SPANISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::FRENCH));

        // delete english value only
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::SPANISH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::FRENCH));
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ANY));

        // delete any
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ANY);
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::SPANISH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::FRENCH));
        $this->assertFalse($sut->has(Attributes::CORE_TEST_STRING, LanguageCodes::ANY));
    }

    public function testKeys(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set(Attributes::CORE_TEST_STRING, 150, LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING_2, 100, LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING_3, 250, LanguageCodes::SPANISH);
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2, Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::ANY));

        // delete value
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH);
        $this->assertEquals([Attributes::CORE_TEST_STRING_2], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_2, Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::ANY));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set(Attributes::CORE_TEST_STRING, 'bar1', LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING_2, 'bar2', LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING_3, 'bar3', LanguageCodes::SPANISH);
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2, Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::ANY));

        // delete value
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH);
        $this->assertEquals([Attributes::CORE_TEST_STRING_2], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING_2, Attributes::CORE_TEST_STRING_3], $sut->keys(LanguageCodes::ANY));
    }

    public function testAll(): void
    {
        /**
         * Test Case #1 - Invariant to language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertEquals([], $sut->keys(LanguageCodes::ANY));

        // set values
        $sut->set(Attributes::CORE_TEST_STRING, 150, LanguageCodes::ENGLISH);
        $this->assertEquals([Attributes::CORE_TEST_STRING => 150], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([], $sut->all(LanguageCodes::FRENCH));

        // delete value
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([], $sut->all(LanguageCodes::FRENCH));

        /**
         * Test Case #2 - Multi-language.
         */
        $sut = new Snapshot($this->getAttributeMangager());
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));

        // set values
        $sut->set(Attributes::CORE_TEST_STRING, 'english value', LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'spanish value', LanguageCodes::SPANISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'french value', LanguageCodes::FRENCH);
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'english value'], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'spanish value'], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'french value'], $sut->all(LanguageCodes::FRENCH));

        // confirm exception
        try {
            $sut->all(LanguageCodes::ANY);
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        // delete
        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'spanish value'], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'french value'], $sut->all(LanguageCodes::FRENCH));

        $sut->del(Attributes::CORE_TEST_STRING, LanguageCodes::ANY);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::SPANISH));
        $this->assertEquals([], $sut->all(LanguageCodes::FRENCH));
    }

    public function testDecodeSnapshot(): void
    {
        $json = '{"type":"SNAPSHOT","version":10,"data":{"foo1":{"type":"STRING","val":[{"type":"TRANS","val":"bar1","ver":0,"lang":"en"},{"type":"TRANS","val":"bar2","ver":0,"lang":"es"}]}}}';
        $sut = $this->getSerializer()->decode($json);
        $this->assertEquals(10, $sut->getVersion());
        // @phpstan-ignore-next-line
        $this->assertTrue($sut->has('foo1', LanguageCodes::ANY));
    }

    public function testFromArrayExceptions(): void
    {
        $sut = new Snapshot($this->getAttributeMangager());

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
                'type' => 'SNAPSHOT', 'data' => [],
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Missing Field: version.', $e->getMessage());
        }

        // missing data
        try {
            $sut->fromArray([
                'type' => 'SNAPSHOT', 'version' => 10,
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Missing Field: data.', $e->getMessage());
        }

        // invalid type
        try {
            $sut->fromArray([
                'type' => 'FOO', 'version' => 10, 'data' => [],
            ]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating SNAPSHOT data type - Invalid Type: FOO.', $e->getMessage());
        }
    }
}
