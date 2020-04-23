<?php

declare(strict_types=1);

namespace App\Tests\DataModel;

use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use PHPUnit\Framework\TestCase;

class SnapshotTest extends TestCase
{
    public function testConstructor(): void
    {
        $sut = new Snapshot(LanguageCodes::ENGLISH);
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLanguage());
    }

    public function testSetGetHas(): void
    {
        $sut = new Snapshot(LanguageCodes::ENGLISH);
        $this->assertFalse($sut->has('foo'));
        $sut->set('foo', 'bar');
        $this->assertEquals('bar', $sut->get('foo'));
        $this->assertTrue($sut->has('foo'));
        $this->assertEquals(['foo' => 'bar'], $sut->all());
        $sut->del('foo');
        $this->assertFalse($sut->has('foo'));
    }
}
