<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Snapshot;

use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use PHPUnit\Framework\TestCase;

class SnapshotTest extends TestCase
{
    public function testConstructor(): void
    {
        // naked constructor
        $sut = new Snapshot();
        $this->assertNull($sut->getLanguage());

        // passing along language
        $sut = new Snapshot([], LanguageCodes::ENGLISH);
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLanguage());
    }

    public function testSetGet(): void
    {
        $sut = new Snapshot();
        $this->assertFalse($sut->has('foo'));
        $sut->set('foo', 'bar');
        $this->assertEquals('bar', $sut->get('foo'));
        $this->assertTrue($sut->has('foo'));
    }

    public function testDel(): void
    {
        $sut = new Snapshot();
        $this->assertFalse($sut->has('foo'));
        $sut->set('foo', 'bar');
        $this->assertTrue($sut->has('foo'));
        $sut->del('foo');
        $this->assertFalse($sut->has('foo'));
    }

    public function testKeys(): void
    {
        $sut = new Snapshot();
        $this->assertEquals([], $sut->keys());
        $sut->set('foo1', 'bar1');
        $sut->set('foo2', 'bar2');
        $sut->set('foo3', 'bar3');
        $this->assertEquals(['foo1', 'foo2', 'foo3'], $sut->keys());
        $sut->del('foo2');
        $this->assertEquals(['foo1', 'foo3'], $sut->keys());
    }

    public function testAll(): void
    {
        $sut = new Snapshot();
        $this->assertEquals([], $sut->all());
        $sut->set('foo1', 'bar1');
        $sut->set('foo2', 'bar2');
        $sut->set('foo3', 'bar3');
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2', 'foo3' => 'bar3'], $sut->all());
        $sut->del('foo2');
        $this->assertEquals(['foo1' => 'bar1', 'foo3' => 'bar3'], $sut->all());
    }

    public function testWasDeleted(): void
    {
        $sut = new Snapshot();
        $this->assertFalse($sut->wasDeleted('foo1'));
        $sut->set('foo1', 'bar1');
        $this->assertFalse($sut->wasDeleted('foo1'));
        $sut->del('foo1');
        $this->assertTrue($sut->wasDeleted('foo1'));
    }

    public function testGetDeletedKeys(): void
    {
        $sut = new Snapshot();
        $sut->set('foo1', 'bar1');
        $sut->del('foo1');
        $this->assertEquals(['foo1'], $sut->getDeletedKeys());
    }
}
