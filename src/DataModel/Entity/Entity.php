<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Snapshot\Snapshot;

class Entity
{
    /**
     * @var EntityId|null
     */
    protected $entityId = null;

    /**
     * @var Snapshot
     */
    protected $snapshot;

    /**
     * Entity constructor.
     *
     * @param string|null $lang
     * @param int|null    $entityType
     */
    public function __construct(array $data = [], $lang = null, $entityType = null)
    {
        $this->snapshot = new Snapshot($data, $lang, $entityType);
    }

    /**
     * Get the language of this Entity.
     */
    public function getLang(): ?string
    {
        return $this->snapshot->getLanguage();
    }

    /**
     * Get the EntityType for this Entity.
     */
    public function getEntityType(): ?int
    {
        return $this->snapshot->getEntityType();
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
        return $this->snapshot->get($key);
    }

    /**
     * Set the value associated with a key.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): void
    {
        $this->snapshot->set($key, $value);
    }

    /**
     * Returns TRUE if entity has key, FALSE otherwise.
     * If entity was deleted then will return FALSE.
     *
     * @param string $key
     */
    public function has($key): bool
    {
        if ($this->snapshot->wasDeleted($key)) {
            return false;
        }

        return $this->snapshot->has($key);
    }

    /**
     * Delete a key from an Entity.
     *
     * @param string $key
     */
    public function del($key): void
    {
        $this->snapshot->del($key);
    }

    /**
     * Return all the data associated with this Entity.
     */
    public function all(): array
    {
        return $this->snapshot->all();
    }

    /**
     * Return the keys associaated with this Entity.
     */
    public function keys(): array
    {
        return $this->snapshot->keys();
    }

    /**
     * Returns TRUE if key was deleted from Entity, FALSE otherwise.
     *
     * @param string $key
     */
    public function wasDeleted($key): bool
    {
        return $this->snapshot->wasDeleted($key);
    }

    /**
     * Returns array of keys deleted from this entity.
     */
    public function getDeletedKeys(): array
    {
        return $this->snapshot->getDeletedKeys();
    }
}
