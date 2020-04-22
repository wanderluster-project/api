<?php

declare(strict_types=1);

namespace App\Tests\EntityManager;

use App\EntityManager\EntityTypeManager;
use App\EntityManager\Persistence\ShardCoordinator;
use App\EntityManager\Persistence\UuidStorage;
use App\EntityManager\UuidManager;
use PHPUnit\Framework\TestCase;

class UuidManagerTest extends TestCase
{
    public function testGenerateUuid(): void
    {
        $shardCoordinator = new ShardCoordinator(0, 0);
        $uuidStorage = new UuidStorage();
        $typeCoordinator = new EntityTypeManager();

        $sut = new UuidManager($shardCoordinator, $uuidStorage, $typeCoordinator);
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
