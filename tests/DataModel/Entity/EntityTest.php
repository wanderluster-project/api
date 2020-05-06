<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Translation\LanguageCodes;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityTest extends WebTestCase
{
    public function testConstuctor(): void
    {
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->getLanguages());
        $this->assertEquals(EntityTypes::TEST_ENTITY_TYPE, $sut->getEntityId());
        $this->assertEmpty($sut->all());
        $this->assertNull($sut->getEntityId());
        $this->assertNull($sut->get('foo'));
        $this->assertFalse($sut->has('foo'));
    }

    public function testSetGetHasAll(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertNull($sut->get('foo'));
        $this->assertFalse($sut->has('foo'));
        $this->assertEquals([], $sut->all());

        // Set a value
        $sut->set('foo1', 'bar1');
        $this->assertTrue($sut->has('foo1'));
        $this->assertEquals('bar1', $sut->get('foo1'));
        $this->assertEquals(['foo1' => 'bar1'], $sut->all());

        // Set another value
        $sut->set('foo2', 'bar2');
        $this->assertTrue($sut->has('foo2'));
        $this->assertEquals('bar2', $sut->get('foo2'));
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $sut->all());

        // Remove a value
        $sut->del('foo1');
        $this->assertFalse($sut->has('foo1'));
        $this->assertEquals(null, $sut->get('foo1'));
        $this->assertEquals(['foo2' => 'bar2'], $sut->all());

        // Adding back deleted value
        $sut->set('foo1', 'bar1.1');
        $this->assertTrue($sut->has('foo1'));
        $this->assertEquals('bar1.1', $sut->get('foo1'));
        $this->assertEquals(['foo1' => 'bar1.1', 'foo2' => 'bar2'], $sut->all());
    }

    public function testDel(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->keys());

        // add value
        $sut->set('foo1', 'bar1');
        $this->assertEquals(['foo1'], $sut->keys());

        // add another value
        $sut->set('foo2', 'bar2');
        $this->assertEquals(['foo1', 'foo2'], $sut->keys());

        // delete value
        $sut->del('foo1');
        $this->assertEquals(['foo2'], $sut->keys());

        // delete by setting equal to null
        $sut->set('foo2', null);
        $this->assertEquals([], $sut->keys());
    }

    public function testKeys(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->keys());

        // add value
        $sut->set('foo1', 'bar1');
        $this->assertEquals(['foo1'], $sut->keys());

        // add another value
        $sut->set('foo2', 'bar2');
        $this->assertEquals(['foo1', 'foo2'], $sut->keys());

        // delete value
        $sut->del('foo1');
        $this->assertEquals(['foo2'], $sut->keys());
    }

    public function testMultilanguage(): void
    {
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->all());

        $sut->load(LanguageCodes::ENGLISH);
        $sut->set('animal', 'dog');

        $sut->load(LanguageCodes::SPANISH);
        $sut->set('animal', 'perro');

        $sut->load(LanguageCodes::ENGLISH);
        $this->assertEquals('dog', $sut->get('animal'));
        $this->assertEquals(['animal'], $sut->keys());

        $sut->load(LanguageCodes::SPANISH);
        $this->assertEquals('perro', $sut->get('animal'));
        $this->assertEquals(['animal'], $sut->keys());
    }

    public function testToArray(): void
    {
        // test empty
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 0,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'snapshot_id' => null,
                'data' => [],
            ],
        ], $sut->toArray());

        // test populated
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $sut->set('test.string', 'english string');
        $sut->load('es');
        $sut->set('test.string', 'spanish string');

        $this->assertEquals([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 0,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'snapshot_id' => null,
                'data' => [
                    'test.string' => [
                        'type' => 'STRING',
                        'trans' => [
                            'en' => 'english string',
                            'es' => 'spanish string',
                        ],
                    ],
                ],
            ],
        ], $sut->toArray());
    }
}
