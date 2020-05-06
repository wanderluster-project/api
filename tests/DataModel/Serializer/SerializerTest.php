<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;
use Exception;

class SerializerTest extends FunctionalTest
{
    public function testDecodingExceptions(): void
    {
        // INVALID CLASS PASSED TO DESERIALIZE
        try {
            $this->getSerializer()->decode('{}');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error hydrating UNKNOWN data type - Missing field: type.', $e->getMessage());
        }
    }

    public function testEncodingEntity(): void
    {
        // test empty
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals('{"type":"ENTITY","entity_id":null,"entity_type":0,"snapshot":{"type":"SNAPSHOT","snapshot_id":null,"data":[]}}', $this->getSerializer()->encode($entity));

        // test with data
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $entity->set('foo1', 'bar1');
        $entity->set('foo2', 'bar2');
        $entity->del('foo2');
        $this->assertEquals('{"type":"ENTITY","entity_id":null,"entity_type":0,"snapshot":{"type":"SNAPSHOT","snapshot_id":null,"data":{"foo1":{"type":"STRING","trans":{"en":"bar1"}}}}}', $this->getSerializer()->encode($entity));

        // test with multiple languages
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $entity->load(LanguageCodes::ENGLISH);
        $entity->set('foo1', 'bar1');
        $entity->load(LanguageCodes::SPANISH);
        $entity->set('foo1', 'bar2');
        $this->assertEquals('{"type":"ENTITY","entity_id":null,"entity_type":0,"snapshot":{"type":"SNAPSHOT","snapshot_id":null,"data":{"foo1":{"type":"STRING","trans":{"en":"bar1","es":"bar2"}}}}}', $this->getSerializer()->encode($entity));
    }

    public function testDecodingEntity(): void
    {
        // test empty
        $json = '{"type":"ENTITY","entity_id":null,"entity_type":0,"snapshot":{"type":"SNAPSHOT","snapshot_id":null,"data":[]}}';
        $entity = $this->getSerializer()->decode($json);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(null, $entity->getEntityId());
        $this->assertEquals([], $entity->getLanguages());
        $this->assertEquals(0, $entity->getEntityType());
        $this->assertEquals([], $entity->all());

        // test fully realized
        $json = '{"type":"ENTITY","entity_id":"c7a556c7-6f27-4049-bdbf-963379154a6f","entity_type":0,"snapshot":{"type":"SNAPSHOT","snapshot_id":null,"data":{"foo1":{"type":"STRING","trans":{"en":"bar1","es":"bar2"}}}}}';
        $entity = $this->getSerializer()->decode($json);
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('c7a556c7-6f27-4049-bdbf-963379154a6f', (string) $entity->getEntityId());
        $this->assertTrue($entity->has('foo1'));
        $this->assertEquals('bar1', $entity->get('foo1'));
        $this->assertEquals(0, $entity->getEntityType());
        $this->assertEquals([LanguageCodes::ENGLISH, LanguageCodes::SPANISH], $entity->getLanguages());
    }
}
