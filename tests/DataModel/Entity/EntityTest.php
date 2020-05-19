<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Attributes\Attributes;
use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;

class EntityTest extends FunctionalTest
{
    public function testConstuctor(): void
    {
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
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
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertNull($sut->get('foo'));
        $this->assertFalse($sut->has('foo'));
        $this->assertEquals([], $sut->all());

        // Set a value
        $sut->set(Attributes::CORE_TEST_STRING, 'bar1');
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING));
        $this->assertEquals('bar1', $sut->get(Attributes::CORE_TEST_STRING));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'bar1'], $sut->all());

        // Set another value
        $sut->set(Attributes::CORE_TEST_STRING_2, 'bar2');
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING_2));
        $this->assertEquals('bar2', $sut->get(Attributes::CORE_TEST_STRING_2));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'bar1', Attributes::CORE_TEST_STRING_2 => 'bar2'], $sut->all());

        // Remove a value
        $sut->del(Attributes::CORE_TEST_STRING);
        $this->assertFalse($sut->has('foo1'));
        $this->assertEquals(null, $sut->get('foo1'));
        $this->assertEquals([Attributes::CORE_TEST_STRING_2 => 'bar2'], $sut->all());

        // Adding back deleted value
        $sut->set(Attributes::CORE_TEST_STRING, 'bar1.1');
        $this->assertTrue($sut->has(Attributes::CORE_TEST_STRING));
        $this->assertEquals('bar1.1', $sut->get(Attributes::CORE_TEST_STRING));
        $this->assertEquals([Attributes::CORE_TEST_STRING => 'bar1.1', Attributes::CORE_TEST_STRING_2 => 'bar2'], $sut->all());
    }

    public function testDel(): void
    {
        // Empty entity
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->keys());

        // add value
        $sut->set(Attributes::CORE_TEST_STRING, 'bar1');
        $this->assertEquals([Attributes::CORE_TEST_STRING], $sut->keys());

        // add another value
        $sut->set(Attributes::CORE_TEST_STRING_2, 'bar2');
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2], $sut->keys());

        // delete value
        $sut->del(Attributes::CORE_TEST_STRING);
        $this->assertEquals([Attributes::CORE_TEST_STRING_2], $sut->keys());

        // delete by setting equal to null
        $sut->set(Attributes::CORE_TEST_STRING_2, null);
        $this->assertEquals([], $sut->keys());
    }

    public function testKeys(): void
    {
        // Empty entity
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->keys());

        // add value
        $sut->set(Attributes::CORE_TEST_STRING, 'bar1');
        $this->assertEquals([Attributes::CORE_TEST_STRING], $sut->keys());

        // add another value
        $sut->set(Attributes::CORE_TEST_STRING_2, 'bar2');
        $this->assertEquals([Attributes::CORE_TEST_STRING, Attributes::CORE_TEST_STRING_2], $sut->keys());

        // delete value
        $sut->del(Attributes::CORE_TEST_STRING);
        $this->assertEquals([Attributes::CORE_TEST_STRING_2], $sut->keys());
    }

    public function testGetLanguages(): void
    {
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE);

        $sut->load(LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'dog');
        $sut->load(LanguageCodes::SPANISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'perro');

        $this->assertEquals([LanguageCodes::ENGLISH, LanguageCodes::SPANISH], $sut->getLanguages());
    }

    public function testGetVersion(): void
    {
        // no version set
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals(0, $sut->getVersion());

        // version set
        // @todo handle versioning
    }

    public function testMultilanguage(): void
    {
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->all());

        $sut->load(LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'dog');

        $sut->load(LanguageCodes::SPANISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'perro');

        $sut->load(LanguageCodes::ENGLISH);
        $this->assertEquals('dog', $sut->get(Attributes::CORE_TEST_STRING));
        $this->assertEquals([Attributes::CORE_TEST_STRING], $sut->keys());

        $sut->load(LanguageCodes::SPANISH);
        $this->assertEquals('perro', $sut->get(Attributes::CORE_TEST_STRING));
        $this->assertEquals([Attributes::CORE_TEST_STRING], $sut->keys());
    }

    public function testFromArray(): void
    {
        // empty data
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE);
        $sut->fromArray([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 100,
            'snapshot' => null,
        ]);
        $this->assertEquals('', (string) $sut->getEntityId());
        $this->assertEquals(100, (string) $sut->getEntityType());

        // with some data
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE);
        $sut->fromArray([
            'type' => 'ENTITY',
            'entity_id' => '31159eca-522c-4d09-8a5d-ee3438e6bb6f',
            'entity_type' => 10,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'version' => 100,
                'data' => [
                    'test.string' => [
                        'type' => 'LOCALIZED_STRING',
                        'val' => [
                            ['type' => 'TRANS', 'lang' => 'en', 'val' => 'english string', 'ver' => 10],
                            ['type' => 'TRANS', 'lang' => 'es', 'val' => 'spanish string', 'ver' => 10],
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
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE);
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
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
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
        $sut = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $sut->set(Attributes::CORE_TEST_STRING, 'english string');
        $sut->load('es');
        $sut->set(Attributes::CORE_TEST_STRING, 'spanish string');

        $this->assertEquals([
            'type' => 'ENTITY',
            'entity_id' => null,
            'entity_type' => 10,
            'snapshot' => [
                'type' => 'SNAPSHOT',
                'version' => null,
                'data' => [
                    'core.test.string_1' => [
                        'type' => 'LOCALIZED_STRING',
                        'val' => [
                            ['type' => 'TRANS', 'lang' => 'en', 'val' => 'english string', 'ver' => 0],
                            ['type' => 'TRANS', 'lang' => 'es', 'val' => 'spanish string', 'ver' => 0],
                        ],
                    ],
                ],
            ],
        ], $sut->toArray());
    }
}
