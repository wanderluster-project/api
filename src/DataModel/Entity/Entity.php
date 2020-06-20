<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Attributes\AttributeManager;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Entity implements SerializableInterface
{
    const SERIALIZATION_ID = 'ENTITY';

    protected AttributeManager $attributeManager;
    protected int $entityType;
    protected string $lang;
    protected EntityId $entityId;
    protected Snapshot $snapshot;

    /**
     * Entity constructor.
     */
    public function __construct(AttributeManager $attributeManager, int $defaultEntityType = 0, string $defaultLang = LanguageCodes::ANY)
    {
        $this->attributeManager = $attributeManager;
        $this->entityType = $defaultEntityType;
        $this->lang = $defaultLang;
        $this->entityId = new EntityId();
        $this->snapshot = new Snapshot($attributeManager);
    }

    /**
     * Load a language,version combination of this Entity.
     */
    public function load(string $lang, int $version = null): void
    {
        $this->lang = $lang;

        // @todo actually load version
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->snapshot->getLanguages();
    }

    public function getVersion(): ?int
    {
        return $this->snapshot->getVersion();
    }

    /**
     * Get the EntityType for this Entity.
     */
    public function getEntityType(): ?int
    {
        return $this->entityType;
    }

    /**
     * Get the EntityId for this Entity.
     */
    public function getEntityId(): ?EntityId
    {
        return $this->entityId;
    }

    /**
     * Get the value associated with a key.
     *
     * @param string $key
     */
    public function get($key): ?string
    {
        return $this->snapshot->get($key, $this->lang);
    }

    /**
     * Set the value associated with a key.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): Entity
    {
        if (is_null($value)) {
            $this->del($key);
        } else {
            $this->snapshot->set($key, $value, $this->lang);
        }

        return $this;
    }

    /**
     * Returns TRUE if entity has key, FALSE otherwise.
     * If entity was deleted then will return FALSE.
     */
    public function has(string $key): bool
    {
        return $this->snapshot->has($key, $this->lang);
    }

    /**
     * Delete a key from an Entity.
     */
    public function del(string $key): void
    {
        $this->snapshot->del($key, $this->lang);
    }

    /**
     * Return all the data associated with this Entity.
     */
    public function all(): array
    {
        return $this->snapshot->all($this->lang);
    }

    /**
     * Return the keys associated with this Entity.
     */
    public function keys(): array
    {
        return $this->snapshot->keys($this->lang);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $entityId = (string) $this->getEntityId();

        return [
            'type' => $this->getSerializationId(),
            'entity_id' => $entityId ? $entityId : null,
            'entity_type' => $this->getEntityType(),
            'snapshot' => $this->snapshot->toArray(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'entity_id', 'entity_type', 'snapshot'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $entityId = $data['entity_id'];
        $entityType = $data['entity_type'];
        $snapshot = $data['snapshot'];

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }

        if ($entityId) {
            $this->entityId = new EntityId($entityId);
        }

        if (!is_int($entityType)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'EntityType should be an integer'));
        }

        if ($entityType) {
            $this->entityType = $entityType;
        }

        if (!is_array($snapshot) && !is_null($snapshot)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Snapshot'));
        }

        if ($snapshot) {
            $this->snapshot->fromArray($snapshot);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializationId(): string
    {
        return self::SERIALIZATION_ID;
    }
}
