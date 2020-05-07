<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Serializer\SerializableInterface;
use App\DataModel\Snapshot\Snapshot;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Entity implements SerializableInterface
{
    /**
     * @var EntityId|null
     */
    protected $entityId = null;

    /**
     * @var int
     */
    protected $entityType = null;

    /**
     * @var Snapshot
     */
    protected $snapshot = null;

    /**
     * @var string|null
     */
    protected $lang = null;

    /**
     * Entity constructor.
     *
     * @param string|null $defaultLang
     */
    public function __construct(int $defaultEntityType = null, $defaultLang = null)
    {
        $this->entityType = $defaultEntityType;
        $this->lang = $defaultLang;
        $this->entityId = new EntityId();
        $this->snapshot = new Snapshot();
    }

    /**
     * @param string   $lang
     * @param int|null $version
     */
    public function load($lang, $version = null): void
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
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): Entity
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
     *
     * @param string $key
     */
    public function has($key): bool
    {
        return $this->snapshot->has($key, $this->lang);
    }

    /**
     * Delete a key from an Entity.
     *
     * @param string $key
     */
    public function del($key): void
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
            'type' => $this->getTypeId(),
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
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $entityId = $data['entity_id'];
        $entityType = $data['entity_type'];
        $snapshot = $data['snapshot'];

        if ($type !== $this->getTypeId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Type: '.$type));
        }

        if ($entityId) {
            $this->entityId = new EntityId($entityId);
        }

        if (!is_int($entityType)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'EntityType should be an integer'));
        }

        if ($entityType) {
            $this->entityType = $entityType;
        }

        if (!is_array($snapshot) && !is_null($snapshot)) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getTypeId(), 'Invalid Snapshot'));
        }

        if ($snapshot) {
            $this->snapshot->fromArray($snapshot);
        }

        return $this;
    }

    public function getTypeId(): string
    {
        return 'ENTITY';
    }
}
