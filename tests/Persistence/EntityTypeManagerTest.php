<?php

declare(strict_types=1);

namespace App\Tests\Persistence;

use App\Persistence\EntityTypeManager;
use PHPUnit\Framework\TestCase;

class EntityTypeManagerTest extends TestCase
{
    public function testIsValidType(): void
    {
        $sut = new EntityTypeManager();
        $this->assertFalse($sut->isValidType('foo'));
        $this->assertFalse($sut->isValidType(-1));
        $this->assertFalse($sut->isValidType(null));
        $this->assertTrue($sut->isValidType(0));
        $this->assertTrue($sut->isValidType(2000));
        $this->assertFalse($sut->isValidType(1000000 + 1));
    }
}
