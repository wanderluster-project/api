<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Translation\LanguageCodes;
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
     * @var EntityUtilites
     */
    protected $entityUtilities;

    /**
     * @var LanguageCodes
     */
    protected $languageCodes;

    /**
     * EntityManager constructor.
     */
    public function __construct(ShardCoordinator $shardCoordinator, EntityIdStorage $entityIdStorage, EntityTypeManager $typeCoordinator, EntityUtilites $entityUtilites, LanguageCodes $languageCodes)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->entityIdStorage = $entityIdStorage;
        $this->typeCoordinator = $typeCoordinator;
        $this->entityUtilities = $entityUtilites;
        $this->languageCodes = $languageCodes;
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

    /**
     * @param string|null $lang
     * @param int|null    $entityType
     *
     * @return Entity
     *
     * @throws WanderlusterException
     */
    public function commit(Entity $entity, $lang = null, $entityType = null)
    {
        if ($lang) {
            $this->entityUtilities->setLang($entity, $lang);
        }

        if ($entityType) {
            $this->entityUtilities->setEntityType($entity, $entityType);
        }

        $entityId = $entity->getEntityId();
        $entityType = $entity->getEntityType();
        $lang = $entity->getLang();

        if (is_null($lang) || !in_array($lang, $this->languageCodes->getLanguageCodes())) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_LANGUAGE_CODE, $lang));
        }

        if (is_null($entityType)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
        }

        if (!$entityId) {
            $this->entityUtilities->setEntityId($entity, $this->allocateEntityId(uniqid(), $entityType));
        }

        return $entity;
    }
}
