<?php

declare(strict_types=1);

namespace App\Sharding;

use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

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
     * @var TypeCoordinator
     */
    protected $typeCoordinator;

    /**
     * UuidFactory constructor.
     * @param ShardCoordinator $shardCoordinator
     * @param UuidStorage $uuidStorage
     */
    public function __construct(ShardCoordinator $shardCoordinator, UuidStorage $uuidStorage, TypeCoordinator $typeCoordinator)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->uuidStorage = $uuidStorage;
        $this->typeCoordinator = $typeCoordinator;
    }

    /**
     * @param string $slug
     * @param int $type
     * @return Uuid
     * @throws WanderlusterException
     */
    public function generateUUID(string $slug, int $entityTypeId): Uuid
    {
        $shard = $this->shardCoordinator->getAvailableShard();
        $identifier = substr(md5($slug), 0, 16);

        if (!$this->typeCoordinator->isValidType($entityTypeId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityTypeId));
        }

        $uuid = new Uuid($shard . '-' . $entityTypeId . '-' . $identifier);
        $this->uuidStorage->allocate($uuid);

        return $uuid;
    }
}
