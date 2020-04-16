<?php

namespace App\Tests\Sharding;

use App\Sharding\Shard;
use PHPUnit\Framework\TestCase;

class ShardTest extends TestCase
{
    public function testConstructor()
    {
        $sut = new Shard(10);
        $this->assertEquals(10, $sut->getShardId());
    }
}
