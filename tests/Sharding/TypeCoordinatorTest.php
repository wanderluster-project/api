<?php

namespace App\Tests\Sharding;

use App\Sharding\TypeCoordinator;
use PHPUnit\Framework\TestCase;

class TypeCoordinatorTest extends TestCase
{
    public function testIsValidType()
    {
        $sut = new TypeCoordinator();
        $this->assertFalse($sut->isValidType(0));
        $this->assertTrue($sut->isValidType(100));
        $this->assertTrue($sut->isValidType(2000));
        $this->assertFalse($sut->isValidType(2001));
    }
}
