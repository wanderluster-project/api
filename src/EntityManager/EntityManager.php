<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\EntityId;
use App\EntityManager\Persistence\EntityIdStorage;
use App\EntityManager\Persistence\ShardCoordinator;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class EntityManager
{
    /**
     * @var ShardCoordinator
     */
    protected $shardCoordinator;

    /**
     * @var EntityIdStorage
     */
    protected $entityIdStorage;

    /**
     * @var EntityTypeManager
     */
    protected $typeCoordinator;

    /**
     * EntityManager constructor.
     */
    public function __construct(ShardCoordinator $shardCoordinator, EntityIdStorage $entityIdStorage, EntityTypeManager $typeCoordinator)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->entityIdStorage = $entityIdStorage;
        $this->typeCoordinator = $typeCoordinator;
    }

    /**
     * @throws WanderlusterException
     */
    public function allocateEntityId(string $slug, int $entityTypeId): EntityId
    {
        $shard = $this->shardCoordinator->getAvailableShard();
        $identifier = substr(md5($slug), 0, 16);

        if (!$this->typeCoordinator->isValidType($entityTypeId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityTypeId));
        }

        $entityId = new EntityId($shard.'-'.$entityTypeId.'-'.$identifier);
        $this->entityIdStorage->allocate($entityId);

        return $entityId;
    }
}
