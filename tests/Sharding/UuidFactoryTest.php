<?php

declare(strict_types=1);

namespace App\Tests\Sharding;

use App\Sharding\ShardCoordinator;
use App\Sharding\TypeCoordinator;
use App\Sharding\UuidFactory;
use App\Sharding\UuidStorage;
use PHPUnit\Framework\TestCase;

class UuidFactoryTest extends TestCase
{
    public function testGenerateUuid(): void
    {
        $shardCoordinator = new ShardCoordinator(0, 0);
        $uuidStorage = new UuidStorage();
        $typeCoordinator = new TypeCoordinator();

        $sut = new UuidFactory($shardCoordinator, $uuidStorage, $typeCoordinator);
        $this->assertEquals('0-100-'.$this->getId('foobar'), $sut->generateUUID('foobar', 100));
    }

    /**
     * Convert string to MD5 substring.
     *
     * @param string $slug
     */
    protected function getId($slug): string
    {
        return substr(md5($slug), 0, 16);
    }
}
