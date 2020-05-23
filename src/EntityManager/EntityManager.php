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
     * @var Entity[]
     */
    protected $trackedEntities = [];

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
     * Create a new Entity.
     */
    public function create(int $defaultEntityType = 0, string $defaultLang = LanguageCodes::ANY): Entity
    {
        $entity = new Entity($this->serializer, $this->attributeManager, $defaultEntityType, $defaultLang);
        $this->allocateEntityId($entity);
        $this->trackedEntities[spl_object_hash($entity)] = $entity;

        return $entity;
    }

    /**
     * @throws WanderlusterException
     */
    public function commit(): self
    {
        foreach ($this->trackedEntities as $entity) {
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
        }

        return $this;
    }

    public function trackEntity(Entity $entity): self
    {
        $this->trackedEntities[spl_object_hash($entity)] = $entity;

        return $this;
    }

    public function stopTrackingEntity(Entity $entity): self
    {
        unset($this->trackedEntities[spl_object_hash($entity)]);

        return $this;
    }

    /**
     * Allocate entity id to entity if it does not exist.
     *
     * @throws WanderlusterException
     * @throws \ReflectionException
     */
    protected function allocateEntityId(Entity $entity): self
    {
        if (!$entity->getEntityId()->isNull()) {
            return $this;
        }

        // @todo persist entity id

        $entityId = new EntityId((string) Uuid::uuid4());
        $reflection = new ReflectionClass(Entity::class);
        $prop = $reflection->getProperty('entityId');
        $prop->setAccessible(true);
        $prop->setValue($entity, $entityId);

        return $this;
    }
}
