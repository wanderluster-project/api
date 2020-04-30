<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Translation\LanguageCodes;
use App\EntityManager\Persistence\ShardCoordinator;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Ramsey\Uuid\Uuid;

class EntityManager
{
    /**
     * @var ShardCoordinator
     */
    protected $shardCoordinator;

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
    public function __construct(ShardCoordinator $shardCoordinator, EntityTypeManager $typeCoordinator, EntityUtilites $entityUtilites, LanguageCodes $languageCodes)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->typeCoordinator = $typeCoordinator;
        $this->entityUtilities = $entityUtilites;
        $this->languageCodes = $languageCodes;
    }

    /**
     * @return Entity
     *
     * @throws WanderlusterException
     */
    public function commit(Entity $entity)
    {
        $entityId = $entity->getEntityId();
        $entityType = $entity->getEntityType();
        $lang = $entity->getLanguage();

        if (is_null($lang) || !in_array($lang, $this->languageCodes->getLanguageCodes())) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_LANGUAGE_CODE, $lang));
        }

        if (is_null($entityType)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
        }

        if (!$entityId) {
            $entityId = $this->generateEntityId();
            $this->entityUtilities->setEntityId($entity, $entityId);
        }

        return $entity;
    }

    /**
     * @throws WanderlusterException
     */
    public function generateEntityId(): EntityId
    {
        return new EntityId((string) Uuid::uuid4());
    }
}
