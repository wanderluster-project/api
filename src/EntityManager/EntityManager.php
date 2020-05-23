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
use ReflectionClass;

class EntityManager
{
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
        LanguageCodes $languageCodes,
        Serializer $serializer,
        AttributeManager $attributeManager,
        EntityTypeManager $entityTypeManager
    ) {
        $this->languageCodes = $languageCodes;
        $this->serializer = $serializer;
        $this->attributeManager = $attributeManager;
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * @throws WanderlusterException
     */
    public function commit(Entity $entity): Entity
    {
        $this->allocateEntityId($entity);
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

        return $entity;
    }

    /**
     * Create a new Entity.
     */
    public function create(int $defaultEntityType = 0, string $defaultLang = LanguageCodes::ANY): Entity
    {
        $entity = new Entity($this->serializer, $this->attributeManager, $defaultEntityType, $defaultLang);
        $this->allocateEntityId($entity);

        return $entity;
    }

    /**
     * Allocate entity id to entity if it does not exist.
     *
     * @throws WanderlusterException
     * @throws \ReflectionException
     */
    protected function allocateEntityId(Entity $entity): Entity
    {
        if (!$entity->getEntityId()->isNull()) {
            return $entity;
        }

        // @todo persist entity id

        $entityId = new EntityId((string) Uuid::uuid4());
        $reflection = new ReflectionClass(Entity::class);
        $prop = $reflection->getProperty('entityId');
        $prop->setAccessible(true);
        $prop->setValue($entity, $entityId);

        return $entity;
    }
}
