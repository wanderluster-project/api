<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\Entity;
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

    public function testNoData(): void
    {
        $sut = new Entity();
        $this->assertNull($sut->getLang());
        $this->assertNull($sut->getEntityId());
        $this->assertNull($sut->getEntityType());
        $this->assertNull($sut->get('foo'));
        $this->assertFalse($sut->has('foo'));
        $sut->set('foo', 'bar');
        $this->assertTrue($sut->has('foo'));
    }

    public function testHasData(): void
    {
        $sut = new Entity(['foo1' => 'bar1'], LanguageCodes::ENGLISH);

        $this->assertNull($sut->getEntityId());
        $this->assertNull($sut->getEntityType());
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLang());

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

    public function testSetGetHasAll(): void
    {
        // Empty entity
        $sut = new Entity();
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
        $sut = new Entity();
        $this->assertEquals([], $sut->keys());

        // add value
        $sut->set('foo1', 'bar1');
        $this->assertEquals(['foo1'], $sut->keys());

        // add another value
        $sut->set('foo2', 'bar2');
        $this->assertEquals(['foo1', 'foo2'], $sut->keys());

        // assert no record of anything being deleted
        $this->assertEquals([], $sut->getDeletedKeys());
        $this->assertFalse($sut->wasDeleted('foo1'));

        // delete value
        $sut->del('foo1');
        $this->assertEquals(['foo2'], $sut->keys());
        $this->assertEquals(['foo1'], $sut->getDeletedKeys());
        $this->assertTrue($sut->wasDeleted('foo1'));

        // delete by setting equal to null
        $sut->set('foo2', null);
        $this->assertEquals([], $sut->keys());
        $this->assertEquals(['foo1', 'foo2'], $sut->getDeletedKeys());
        $this->assertTrue($sut->wasDeleted('foo2'));
    }

    public function testKeys(): void
    {
        // Empty entity
        $sut = new Entity();
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
}