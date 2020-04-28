<?php

declare(strict_types=1);

namespace App\Tests\EntityManager\Persistence;

use App\EntityManager\Persistence\Shard;
use App\EntityManager\Persistence\ShardCoordinator;
use PHPUnit\Framework\TestCase;

class ShardCoordinatorTest extends TestCase
{
    public function testGetAvailableShard(): void
    {
        $min = 0;
        $max = 10;

        $sut = new ShardCoordinator($min, $max);
        for ($i = 0; $i <= 20; ++$i) {
            $this->assertGreaterThanOrEqual($min, $sut->getAvailableShard()->getShardId());
            $this->assertLessThanOrEqual($max, $sut->getAvailableShard()->getShardId());
        }
    }

    public function testIsValidShard(): void
    {
        $min = 0;
        $max = 10;

        $sut = new ShardCoordinator($min, $max);
        $this->assertFalse($sut->isValidShard(new Shard(100)));
        $this->assertTrue($sut->isValidShard(new Shard(0)));
        $this->assertTrue($sut->isValidShard(new Shard(10)));
    }
}
