<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Snapshot\SnapshotId;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\FunctionalTest;
use Exception;
use StdClass;

class SerializerTest extends FunctionalTest
{
    public function testEncodingExceptions(): void
    {
        // RESOURCES SHOULD THROW ERROR RATHER THAN BEING SERIALIZED
        try {
            $this->getSerializer()->encode(fopen('/var/www/wanderluster/tests/Fixtures/Files/sample.jpg', 'r'));
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error serializing - Invalid data type', $e->getMessage());
        }

        // INVALID CLASS PASSED TO SERIALIZE
        try {
            $this->getSerializer()->encode(new StdClass());
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error serializing - Invalid class: stdClass', $e->getMessage());
        }
    }

    public function testDecodingExceptions(): void
    {
        // INVALID CLASS PASSED TO DESERIALIZE
        try {
            $this->getSerializer()->decode('{}', StdClass::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Invalid class: StdClass', $e->getMessage());
        }

        // MISSING ID WHEN DESERIALIZING ENTITY
        try {
            $this->getSerializer()->decode('{}', Entity::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Missing parameter: id', $e->getMessage());
        }

        // MISSING ENTITY TYPE WHEN DESERIALIZING ENTITY
        try {
            $this->getSerializer()->decode('{"id":"09857e03-fca1-45a3-ab98-9cb1702fa1df"}', Entity::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Missing parameter: type', $e->getMessage());
        }

        // MISSING DATA WHEN DESERIALIZING ENTITY
        try {
            $this->getSerializer()->decode('{"id":"09857e03-fca1-45a3-ab98-9cb1702fa1df","type":100}', Entity::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Missing parameter: data', $e->getMessage());
        }
    }

    public function testEncodingScalar(): void
    {
        // null
        $this->assertSame('null', $this->getSerializer()->encode(null));

        // boolean
        $this->assertSame('true', $this->getSerializer()->encode(true));
        $this->assertSame('false', $this->getSerializer()->encode(false));

        // numeric
        $this->assertSame('0', $this->getSerializer()->encode(0));
        $this->assertSame('100', $this->getSerializer()->encode(100));
        $this->assertSame('-7.5', $this->getSerializer()->encode(-7.5));

        // string
        $this->assertSame('foo', $this->getSerializer()->encode('foo'));
    }

    public function testEncodingArray(): void
    {
        // empty array
        $this->assertSame('[]', $this->getSerializer()->encode([]));

        // indexed array
        $this->assertSame('["foo",false,0,-1.4,null]', $this->getSerializer()->encode(['foo', false, 0, -1.4, null]));

        // associative array
        $this->assertSame('{"a":"foo","b":false,"c":0,"d":-1.4,"e":null}', $this->getSerializer()->encode(['a' => 'foo', 'b' => false, 'c' => 0, 'd' => -1.4, 'e' => null]));
    }

    public function testEncodingEntityId(): void
    {
        $entityId = new EntityId('bf838d09-39e8-4619-92b9-3ecdd95bbdd4');
        $this->assertEquals('bf838d09-39e8-4619-92b9-3ecdd95bbdd4', $this->getSerializer()->encode($entityId));
    }

    public function testDecodingEntityId(): void
    {
        $entityId = $this->getSerializer()->decode('fe1ec5b1-f311-40a7-9e53-e0bb4fbab197', EntityId::class);
        $this->assertInstanceOf(EntityId::class, $entityId);
        $this->assertEquals('fe1ec5b1-f311-40a7-9e53-e0bb4fbab197', $entityId->getUuid());
    }

    public function testEncodingSnapshotId(): void
    {
        $snapshotId = new SnapshotId('41693129-fb93-4158-b81f-420840fb4205.es-150');
        $this->assertEquals('41693129-fb93-4158-b81f-420840fb4205.es-150', $this->getSerializer()->encode($snapshotId));
    }

    public function testDecodingSnapshotId(): void
    {
        $snapshotId = $this->getSerializer()->decode('a5f334dd-7953-4faa-b708-c14bfd6213a2.es-50', SnapshotId::class);
        $this->assertInstanceOf(SnapshotId::class, $snapshotId);
        $this->assertEquals('a5f334dd-7953-4faa-b708-c14bfd6213a2', (string) $snapshotId->getEntityId());
        $this->assertEquals(50, $snapshotId->getVersion());
        $this->assertEquals('es', $snapshotId->getLanguage());
    }

    public function testEncodingEntity(): void
    {
        // test empty
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals('{"id":null,"type":0,"data":{}}', $this->getSerializer()->encode($entity));

        // test with data
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $entity->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $entity->set('foo2', 'bar2', LanguageCodes::ENGLISH);
        $entity->del('foo2', LanguageCodes::ENGLISH);
        $this->assertEquals('{"id":null,"type":0,"data":{"en":{"foo1":"bar1"}}}', $this->getSerializer()->encode($entity));

        // test with multiple languages
        $entity = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $entity->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $entity->set('foo2', 'bar2', LanguageCodes::SPANISH);
        $this->assertEquals('{"id":null,"type":0,"data":{"en":{"foo1":"bar1"},"es":{"foo2":"bar2"}}}', $this->getSerializer()->encode($entity));
    }

    public function testDecodingEntity(): void
    {
        // test empty
        $json = '{"id":null,"type":0,"data":[]}';
        $entity = $this->getSerializer()->decode($json, Entity::class);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(null, $entity->getEntityId());
        $this->assertEquals([], $entity->getLanguages());
        $this->assertEquals(0, $entity->getEntityType());
        $this->assertEquals([], $entity->all(LanguageCodes::ENGLISH));

        // test fully realized
        $json = '{"id":"c7a556c7-6f27-4049-bdbf-963379154a6f","type":0,"data":{"en":{"foo1":"bar1"},"es":{"foo2":"bar2"}}}';
        $entity = $this->getSerializer()->decode($json, Entity::class);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('c7a556c7-6f27-4049-bdbf-963379154a6f', (string) $entity->getEntityId());
        $this->assertTrue($entity->has('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals('bar1', $entity->get('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals(0, $entity->getEntityType());
        $this->assertEquals([LanguageCodes::ENGLISH, LanguageCodes::SPANISH], $entity->getLanguages());
    }
}
