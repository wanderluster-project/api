<?php

namespace App\Tests\Sharding;

use App\Sharding\EntityType;
use App\Sharding\TypeCoordinator;
use PHPUnit\Framework\TestCase;

class TypeCoordinatorTest extends TestCase
{
    public function testIsValidType()
    {
        $sut = new TypeCoordinator();
        $this->assertFalse($sut->isValidType(new EntityType(0)));
        $this->assertTrue($sut->isValidType(new EntityType(100)));
        $this->assertTrue($sut->isValidType(new EntityType(2000)));
        $this->assertFalse($sut->isValidType(new EntityType(2001)));
    }
}
