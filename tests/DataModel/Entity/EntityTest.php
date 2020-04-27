<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\Entity;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Translation\LanguageCodes;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityTest extends WebTestCase
{
    public function testConstuctor(): void
    {
        // naked constructor
        $sut = new Entity();
        $this->assertNull($sut->getLang());
        $this->assertNull($sut->getEntityId());
        $this->assertFalse($sut->has('foo'));

        // initialization params
        $sut = new Entity(['foo' => 'bar'], LanguageCodes::ENGLISH);
        $this->assertEquals(null, $sut->getEntityId());
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLang());
        $this->assertTrue($sut->has('foo'));
    }

    public function testNoPreviousSnapshot(): void
    {
        $sut = new Entity();
        $this->assertNull($sut->get('foo'));
        $this->assertFalse($sut->has('foo'));
        $sut->set('foo', 'bar');
        $this->assertTrue($sut->has('foo'));
    }

    public function testHasPreviousSnapshot(): void
    {
        $sut = new Entity(['foo1' => 'bar1'], LanguageCodes::ENGLISH);

        // confirm using previous values
        $this->assertTrue($sut->has('foo1'));
        $this->assertFalse($sut->has('foo2'));

        // set new value
        $sut->set('foo2', 'bar2');
        $this->assertTrue($sut->has('foo2'));

        // remove value
        $sut->del('foo1');
        $this->assertFalse($sut->has('foo1'));
    }

    public function testSerialization(): void
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

    public function testDeserialization(): void
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

    protected function getSerializer(): Serializer
    {
        self::bootKernel();

        return self::$container->get('test.serializer');
    }
}
