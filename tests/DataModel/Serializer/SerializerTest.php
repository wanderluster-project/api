<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
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

        // MISSING LANG WHEN DESERIALIZING ENTITY
        try {
            $this->getSerializer()->decode('{"id":"10-3-3858f62230ac3c91"}', Entity::class);
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Missing parameter: lang', $e->getMessage());
        }

        // MISSING DATA WHEN DESERIALIZING ENTITY
        try {
            $this->getSerializer()->decode('{"id":"10-3-3858f62230ac3c91","lang":"en"}', Entity::class);
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
        $entityId = new EntityId('10-3-3858f62230ac3c91');
        $this->assertEquals('10-3-3858f62230ac3c91', $this->getSerializer()->encode($entityId));
    }

    public function testDecodingEntityId(): void
    {
        $entityId = $this->getSerializer()->decode('10-3-3858f62230ac3c91', EntityId::class);
        $this->assertInstanceOf(EntityId::class, $entityId);
        $this->assertEquals(10, $entityId->getShard());
        $this->assertEquals(3, $entityId->getEntityType());
        $this->assertEquals('3858f62230ac3c91', $entityId->getIdentifier());
    }

    public function testEncodingSnapshotId(): void
    {
        $snapshotId = new SnapshotId('10-3-3858f62230ac3c91.100');
        $this->assertEquals('10-3-3858f62230ac3c91.100', $this->getSerializer()->encode($snapshotId));
    }

    public function testDecodingSnapshotId(): void
    {
        $snapshotId = $this->getSerializer()->decode('10-3-3858f62230ac3c91.100', SnapshotId::class);
        $this->assertInstanceOf(SnapshotId::class, $snapshotId);
        $this->assertEquals('10-3-3858f62230ac3c91', (string) $snapshotId->getEntityId());
        $this->assertEquals(100, $snapshotId->getVersion());
    }

    public function testEncodingEntity(): void
    {
        // test empty
        $entity = new Entity();
        $this->assertEquals('{"id":null,"lang":null,"data":[]}', $this->getSerializer()->encode($entity));

        // test with data
        $entity = new Entity([], LanguageCodes::ENGLISH);
        $entity->set('foo1', 'bar1');
        $entity->set('foo2', 'bar2');
        $entity->del('foo2');
        $this->assertEquals('{"id":null,"lang":"en","data":{"foo1":"bar1"}}', $this->getSerializer()->encode($entity));
    }

    public function testDecodingEntity(): void
    {
        // test empty
        $json = '{"id":null,"lang":null,"data":[]}';
        $entity = $this->getSerializer()->decode($json, Entity::class);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals(null, $entity->getEntityId());
        $this->assertEquals(null, $entity->getLang());
        $this->assertEquals([], $entity->all());

        // test fully realized
        $json = '{"id":"10-3-3858f62230ac3c91","lang":"en","data":{"foo1":"bar1"}}';
        $entity = $this->getSerializer()->decode($json, Entity::class);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('10-3-3858f62230ac3c91', (string) $entity->getEntityId());
        $this->assertTrue($entity->has('foo1'));
        $this->assertEquals('bar1', $entity->get('foo1'));
        $this->assertEquals(LanguageCodes::ENGLISH, $entity->getLang());
    }
}
