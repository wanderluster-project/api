<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Serializer\Serializer;
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
     * @var Serializer
     */
    protected $serializer;

    /**
     * EntityManager constructor.
     */
    public function __construct(ShardCoordinator $shardCoordinator, EntityTypeManager $typeCoordinator, EntityUtilites $entityUtilites, LanguageCodes $languageCodes, Serializer $serializer)
    {
        $this->shardCoordinator = $shardCoordinator;
        $this->typeCoordinator = $typeCoordinator;
        $this->entityUtilities = $entityUtilites;
        $this->languageCodes = $languageCodes;
        $this->serializer = $serializer;
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
        $languages = $entity->getLanguages();

        foreach ($languages as $language) {
            if (!in_array($language, $this->languageCodes->getLanguageCodes())) {
                throw new WanderlusterException(sprintf(ErrorMessages::INVALID_LANGUAGE_CODE, $language));
            }
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

    /**
     * Create a new Entity.
     */
    public function create(int $defaultEntityType = 0, string $defaultLang = LanguageCodes::ANY): Entity
    {
        return new Entity($this->serializer, $defaultEntityType, $defaultLang);
    }
}
