<?php

declare(strict_types=1);

namespace App\Tests\EntityManager;

use App\EntityManager\EntityManager;
use App\EntityManager\EntityTypeManager;
use App\EntityManager\Persistence\EntityIdStorage;
use App\EntityManager\Persistence\ShardCoordinator;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{
    public function testAllocateId(): void
    {
        $shardCoordinator = new ShardCoordinator(0, 0);
        $entityIdStorage = new EntityIdStorage();
        $typeCoordinator = new EntityTypeManager();

        $sut = new EntityManager($shardCoordinator, $entityIdStorage, $typeCoordinator);
        $this->assertEquals('0-100-'.$this->getId('foobar'), $sut->allocateEntityId('foobar', 100));
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
