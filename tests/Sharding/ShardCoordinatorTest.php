<?php

declare(strict_types=1);

namespace App\Tests\Sharding;

use App\Sharding\ShardCoordinator;
use PHPUnit\Framework\TestCase;

class ShardCoordinatorTest extends TestCase
{
    public function testGetAvailableShard()
    {
        $min = 0;
        $max = 10;

        $sut = new ShardCoordinator($min, $max);
        for ($i=0;$i<=20;$i++) {
            $this->assertGreaterThanOrEqual($min, $sut->getAvailableShard());
            $this->assertLessThanOrEqual($max, $sut->getAvailableShard());
        }
    }
}
