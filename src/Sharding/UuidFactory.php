<?php

declare(strict_types=1);

namespace App\Sharding;

class UuidFactory
{
    /**
     * @var ShardCoordinator
     */
    protected $shardCoordinator;

    /**
     * @var UuidStorage
     */
    protected $uuidStorage;

    /**
     * UuidFactory constructor.
     * @param ShardCoordinator $shardCoordinator
     * @param UuidStorage $uuidStorage
     */
    public function __construct(ShardCoordinator $shardCoordinator, UuidStorage $uuidStorage)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->uuidStorage = $uuidStorage;
    }

    /**
     * Generates a unique identifier.
     *
     * @param string $slug
     * @param int $type
     * @return string
     */
    public function generateUUID(string $slug, int $type): string
    {
        $shard = $this->shardCoordinator->getAvailableShard();
        $identifier = substr(md5($slug), 0, 16);

        // @todo add guard rails to check that slug/type/subtype are within appropriate range

        $uuid = dechex($shard) . '-' . dechex($type) . '-' . $identifier;
        $this->uuidStorage->allocate($uuid);

        return $uuid;
    }
}
