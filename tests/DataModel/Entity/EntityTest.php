<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Entity;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testConstuctor(): void
    {
        // naked constructor
        $sut = new Entity();
        $this->assertNull($sut->getLang());
        $this->assertNull($sut->getEntityId());
        $this->assertFalse($sut->has('foo'));

        // initialization params
        $entityId = new EntityId('10-3-3858f62230ac3c91');
        $previousSnapshot = new Snapshot();
        $previousSnapshot->set('foo', 'bar');
        $sut = new Entity($entityId, $previousSnapshot, LanguageCodes::ENGLISH);
        $this->assertEquals($entityId, $sut->getEntityId());
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
        $entityId = new EntityId('10-3-3858f62230ac3c91');
        $previousSnapshot = new Snapshot();
        $previousSnapshot->set('foo1', 'bar1');
        $sut = new Entity($entityId, $previousSnapshot, LanguageCodes::ENGLISH);

        // confirm using previous values
        $this->assertTrue($sut->has('foo1'));
        $this->assertFalse($sut->has('foo2'));

        // set new value
        $sut->set('foo2', 'bar2');
        $this->assertTrue($sut->has('foo2'));
        $this->assertFalse($previousSnapshot->has('foo2'));

        // remove value
        $sut->del('foo1');
        $this->assertTrue($previousSnapshot->has('foo1'));
        $this->assertFalse($sut->has('foo1'));
    }
}
