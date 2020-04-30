<?php

declare(strict_types=1);

namespace App\Tests\EntityManager;

use App\EntityManager\EntityTypeManager;
use PHPUnit\Framework\TestCase;

class EntityTypeManagerTest extends TestCase
{
    public function testIsValidType(): void
    {
        $sut = new EntityTypeManager();
        $this->assertFalse($sut->isValidType(0 - 1));
        $this->assertTrue($sut->isValidType(0));
        $this->assertTrue($sut->isValidType(2000));
        $this->assertFalse($sut->isValidType(1000000 + 1));
    }
}
