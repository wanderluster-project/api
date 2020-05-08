<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityTest extends WebTestCase
{
    public function testConstuctor(): void
    {
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->getLanguages());
        $this->assertEquals(EntityTypes::TEST_ENTITY_TYPE, $sut->getEntityType());
        $this->assertEmpty($sut->all());
        $this->assertEquals('', (string) $sut->getEntityId());
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

    public function testGetLanguages(): void
    {
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);

        $sut->load(LanguageCodes::ENGLISH);
        $sut->set('animal', 'dog');
        $sut->load(LanguageCodes::SPANISH);
        $sut->set('animal', 'perro');

        $this->assertEquals([LanguageCodes::ENGLISH, LanguageCodes::SPANISH], $sut->getLanguages());
    }

    public function testGetVersion(): void
    {
        // no version set
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals(0, $sut->getVersion());

        // version set
        // @todo handle versioning
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

    public function testFromArray(): void
    {
        // empty data
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $sut->fromArray([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 100,
            'snapshot' => null,
        ]);
        $this->assertEquals('', (string) $sut->getEntityId());
        $this->assertEquals(100, (string) $sut->getEntityType());

        // with some data
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $sut->fromArray([
            'type' => 'ENTITY',
            'entity_id' => '31159eca-522c-4d09-8a5d-ee3438e6bb6f',
            'entity_type' => 10,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'version' => 100,
                'data' => [
                    'test.string' => [
                        'type' => 'STRING',
                        'ver' => 0,
                        'val' => [
                            'en' => 'english string',
                            'es' => 'spanish string',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('31159eca-522c-4d09-8a5d-ee3438e6bb6f', (string) $sut->getEntityId());
        $this->assertEquals(10, $sut->getEntityType());
        $this->assertEquals(100, $sut->getVersion());
    }

    public function testFromArrayExceptions(): void
    {
        // Missing Type
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Missing Field: type.', $e->getMessage());
        }

        // Missing EntityId
        try {
            $sut->fromArray([
                'type' => 'ENTITY',
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Missing Field: entity_id.', $e->getMessage());
        }

        // Missing EntityType
        try {
            $sut->fromArray([
                'type' => 'ENTITY',
                'entity_id' => null,
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Missing Field: entity_type.', $e->getMessage());
        }

        // Missing Snapshot
        try {
            $sut->fromArray([
                'type' => 'ENTITY',
                'entity_id' => null,
                'entity_type' => 100,
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Missing Field: snapshot.', $e->getMessage());
        }

        // Invalid EntityType
        try {
            $sut->fromArray([
                'type' => 'ENTITY',
                'entity_id' => null,
                'entity_type' => 'INVALID',
                'snapshot' => null,
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - EntityType should be an integer.', $e->getMessage());
        }

        // Invalid Snapshot
        try {
            $sut->fromArray([
                'type' => 'ENTITY',
                'entity_id' => null,
                'entity_type' => 100,
                'snapshot' => 'INVALID',
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Invalid Snapshot.', $e->getMessage());
        }

        // Invalid Type
        try {
            $sut->fromArray([
                'type' => 'FOO',
                'entity_id' => null,
                'entity_type' => 100,
                'snapshot' => null,
            ]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating ENTITY data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testToArray(): void
    {
        // test empty
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 10,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'version' => null,
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
            'entity_type' => 10,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'version' => null,
                'data' => [
                    'test.string' => [
                        'type' => 'STRING',
                        'ver' => 0,
                        'val' => [
                            'en' => 'english string',
                            'es' => 'spanish string',
                        ],
                    ],
                ],
            ],
        ], $sut->toArray());
    }
}
