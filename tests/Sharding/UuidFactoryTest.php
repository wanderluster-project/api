<?php

declare(strict_types=1);

namespace App\Tests\Sharding;

use App\Sharding\ShardCoordinator;
use App\Sharding\UuidFactory;
use App\Sharding\UuidStorage;
use PHPUnit\Framework\TestCase;

class UuidFactoryTest extends TestCase
{
    public function testGenerateUuid()
    {
        $shardCoordinator = new ShardCoordinator(0, 0);
        $uuidStorage = new UuidStorage();

        $sut = new UuidFactory($shardCoordinator, $uuidStorage);
        $this->assertEquals('0-0-'.$this->getId('foobar'), $sut->generateUUID('foobar', 0));
    }

    protected function getId($slug)
    {
        return substr(md5($slug), 0, 16);
    }
}
