<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Serializer;

use App\DataModel\Attributes\Attributes;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityTypes;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\Fixtures\TestType;
use App\Tests\FunctionalTest;
use Exception;

class SerializerTest extends FunctionalTest
{
    public function testEncodingException(): void
    {
        $invalidObj = new class() implements SerializableInterface {
            public function getSerializationId(): string
            {
                return 'INVALID';
            }

            public function toArray(): array
            {
                return [
                  'foo' => fopen(__DIR__.'/../../Fixtures/mime-types.txt', 'r'),
                ];
            }

            public function fromArray(array $data): SerializableInterface
            {
                return $this;
            }

            public function setSerializer(Serializer $serializer): SerializableInterface
            {
                return $this;
            }
        };

        // resources are not serializable so this should throw errr.
        try {
            $sut = $this->getSerializer()->encode($invalidObj);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error serializing - Error encountered encoding to JSON.', $e->getMessage());
        }
    }

    public function testDecodingExceptions(): void
    {
        // INVALID JSON PASSED TO DESERIALIZE
        try {
            $this->getSerializer()->decode('["test" : 123]');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error serializing - Error encountered decoding from JSON.', $e->getMessage());
        }

        // INVALID DATA PASSED TO DESERIALIZE
        try {
            $this->getSerializer()->decode('{}');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error hydrating UNKNOWN data type - Missing field: type.', $e->getMessage());
        }

        // INVALID TYPE
        try {
            $this->getSerializer()->decode('{"type": "INVALID"}');
            $this->fail('Exception not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(WanderlusterException::class, $e);
            $this->assertEquals('Error deserializing - Invalid type: INVALID.', $e->getMessage());
        }
    }

    public function testEncodingEntity(): void
    {
        // test empty
        $entity = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $this->assertEquals('{"type":"ENTITY","entity_id":"'.$entity->getEntityId().'","entity_type":10,"snapshot":{"type":"SNAPSHOT","version":null,"data":[]}}', $this->getSerializer()->encode($entity));

        // test with data
        $entity = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $entity->set(Attributes::CORE_TEST_STRING, 'bar1');
        $entity->set(Attributes::CORE_TEST_STRING_2, 'bar2');
        $entity->del(Attributes::CORE_TEST_STRING_2);
        $this->assertEquals('{"type":"ENTITY","entity_id":"'.$entity->getEntityId().'","entity_type":10,"snapshot":{"type":"SNAPSHOT","version":null,"data":{"core.test.string_1":{"type":"STRING","val":[{"type":"TRANS","val":"bar1","ver":0,"lang":"en"}]}}}}', $this->getSerializer()->encode($entity));

        // test with multiple languages
        $entity = $this->getEntityManager()->create(EntityTypes::TEST_ENTITY_TYPE, LanguageCodes::ENGLISH);
        $entity->load(LanguageCodes::ENGLISH);
        $entity->set(Attributes::CORE_TEST_STRING, 'bar1');
        $entity->load(LanguageCodes::SPANISH);
        $entity->set(Attributes::CORE_TEST_STRING, 'bar2');
        $this->assertEquals('{"type":"ENTITY","entity_id":"'.$entity->getEntityId().'","entity_type":10,"snapshot":{"type":"SNAPSHOT","version":null,"data":{"core.test.string_1":{"type":"STRING","val":[{"type":"TRANS","val":"bar1","ver":0,"lang":"en"},{"type":"TRANS","val":"bar2","ver":0,"lang":"es"}]}}}}', $this->getSerializer()->encode($entity));
    }

    public function testDecodingEntity(): void
    {
        // test empty
        $json = '{"type":"ENTITY","entity_id":"","entity_type":10,"snapshot":{"type":"SNAPSHOT","version":null,"data":[]}}';
        $entity = $this->getSerializer()->decode($json);
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('', $entity->getEntityId());
        $this->assertEquals([], $entity->getLanguages());
        $this->assertEquals(EntityTypes::TEST_ENTITY_TYPE, $entity->getEntityType());
        $this->assertEquals([], $entity->all());

        // test fully realized
        $json = '{"type":"ENTITY","entity_id":"c7a556c7-6f27-4049-bdbf-963379154a6f","entity_type":10,"snapshot":{"type":"SNAPSHOT","version":null,"data":{"foo1":{"type":"STRING","val":[{"type":"TRANS","val":"bar1","ver":0,"lang":"en"},{"type":"TRANS","val":"bar2","ver":0,"lang":"es"}]}}}}';
        $entity = $this->getSerializer()->decode($json);
        $entity->load(LanguageCodes::ENGLISH);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('c7a556c7-6f27-4049-bdbf-963379154a6f', (string) $entity->getEntityId());
        $this->assertTrue($entity->has('foo1'));
        $this->assertEquals('bar1', $entity->get('foo1'));
        $this->assertEquals(EntityTypes::TEST_ENTITY_TYPE, $entity->getEntityType());
        $this->assertEquals([LanguageCodes::ENGLISH, LanguageCodes::SPANISH], $entity->getLanguages());
    }

    public function testRegisterType(): void
    {
        $sut = $this->getSerializer();
        $sut->registerType(TestType::class);
        try {
            $sut->registerType(TestType::class);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Data type already registered with Serializer - TEST.', $e->getMessage());
        }

        $sut->registerType(TestType::class, true);
        $this->assertTrue($sut->isTypeRegistered('TEST'));
    }
}
