<?php

declare(strict_types=1);

namespace App\EntityManager;

use App\DataModel\Attributes\AttributeManager;
use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Serializer\Serializer;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;
use Ramsey\Uuid\Uuid;

class EntityManager
{
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
     * @var AttributeManager
     */
    protected $attributeManager;

    /**
     * @var EntityTypeManager
     */
    protected $entityTypeManager;

    /**
     * EntityManager constructor.
     */
    public function __construct(
        EntityTypeManager $typeCoordinator,
        EntityUtilites $entityUtilites,
        LanguageCodes $languageCodes,
        Serializer $serializer,
        AttributeManager $attributeManager,
        EntityTypeManager $entityTypeManager
    ) {
        $this->typeCoordinator = $typeCoordinator;
        $this->entityUtilities = $entityUtilites;
        $this->languageCodes = $languageCodes;
        $this->serializer = $serializer;
        $this->attributeManager = $attributeManager;
        $this->entityTypeManager = $entityTypeManager;
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

        if (!$this->entityTypeManager->isValidType($entity->getEntityType())) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
        }

        if ($entityId->isNull()) {
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
        return new Entity($this->serializer, $this->attributeManager, $defaultEntityType, $defaultLang);
    }
}
