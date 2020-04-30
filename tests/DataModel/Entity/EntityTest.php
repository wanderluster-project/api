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
        $this->assertEmpty($sut->all(LanguageCodes::ENGLISH));
        $this->assertNull($sut->getEntityId());
        $this->assertNull($sut->get('foo', LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::ENGLISH));
    }

    public function testSetGetHasAll(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertNull($sut->get('foo', LanguageCodes::ENGLISH));
        $this->assertFalse($sut->has('foo', LanguageCodes::ENGLISH));
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));

        // Set a value
        $sut->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $this->assertTrue($sut->has('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals('bar1', $sut->get('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1' => 'bar1'], $sut->all(LanguageCodes::ENGLISH));

        // Set another value
        $sut->set('foo2', 'bar2', LanguageCodes::ENGLISH);
        $this->assertTrue($sut->has('foo2', LanguageCodes::ENGLISH));
        $this->assertEquals('bar2', $sut->get('foo2', LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $sut->all(LanguageCodes::ENGLISH));

        // Remove a value
        $sut->del('foo1', LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut->get('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals(['foo2' => 'bar2'], $sut->all(LanguageCodes::ENGLISH));

        // Adding back deleted value
        $sut->set('foo1', 'bar1.1', LanguageCodes::ENGLISH);
        $this->assertTrue($sut->has('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals('bar1.1', $sut->get('foo1', LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1' => 'bar1.1', 'foo2' => 'bar2'], $sut->all(LanguageCodes::ENGLISH));
    }

    public function testDel(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->keys(LanguageCodes::ENGLISH));

        // add value
        $sut->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo1'], $sut->keys(LanguageCodes::ENGLISH));

        // add another value
        $sut->set('foo2', 'bar2', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo1', 'foo2'], $sut->keys(LanguageCodes::ENGLISH));

        // assert no record of anything being deleted
        $this->assertEquals([], $sut->getDeletedKeys(LanguageCodes::ENGLISH));
        $this->assertFalse($sut->wasDeleted('foo1', LanguageCodes::ENGLISH));

        // delete value
        $sut->del('foo1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo2'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1'], $sut->getDeletedKeys(LanguageCodes::ENGLISH));
        $this->assertTrue($sut->wasDeleted('foo1', LanguageCodes::ENGLISH));

        // delete by setting equal to null
        $sut->set('foo2', null, LanguageCodes::ENGLISH);
        $this->assertEquals([], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['foo1', 'foo2'], $sut->getDeletedKeys(LanguageCodes::ENGLISH));
        $this->assertTrue($sut->wasDeleted('foo2', LanguageCodes::ENGLISH));
    }

    public function testKeys(): void
    {
        // Empty entity
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->keys(LanguageCodes::ENGLISH));

        // add value
        $sut->set('foo1', 'bar1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo1'], $sut->keys(LanguageCodes::ENGLISH));

        // add another value
        $sut->set('foo2', 'bar2', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo1', 'foo2'], $sut->keys(LanguageCodes::ENGLISH));

        // delete value
        $sut->del('foo1', LanguageCodes::ENGLISH);
        $this->assertEquals(['foo2'], $sut->keys(LanguageCodes::ENGLISH));
    }

    public function testMultilanguage(): void
    {
        $sut = new Entity(EntityTypes::TEST_ENTITY_TYPE);
        $this->assertEquals([], $sut->all(LanguageCodes::ENGLISH));

        $sut->set('animal', 'dog', LanguageCodes::ENGLISH);
        $sut->set('animal', 'perro', LanguageCodes::SPANISH);
        $this->assertEquals('dog', $sut->get('animal', LanguageCodes::ENGLISH));
        $this->assertEquals('perro', $sut->get('animal', LanguageCodes::SPANISH));
        $this->assertEquals(['animal'], $sut->keys(LanguageCodes::ENGLISH));
        $this->assertEquals(['animal'], $sut->keys(LanguageCodes::SPANISH));
    }
}
