<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Snapshot\Snapshot;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Entity
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
     * @var Snapshot[]
     */
    protected $snapshots = [];

    /**
     * Entity constructor.
     */
    public function __construct(int $entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return array_keys($this->snapshots);
    }

    /**
     * Get the EntityType for this Entity.
     */
    public function getEntityType(): ?int
    {
        return $this->entityType;
    }

    /**
     * Set the EntityType for this Entity.
     */
    public function setEntityType(int $entityType): void
    {
        $this->entityType = $entityType;
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
     * @param string $lang
     */
    public function get($key, $lang): ?string
    {
        return $this->getSnapshot($lang)->get($key);
    }

    /**
     * Set the value associated with a key.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $lang
     */
    public function set($key, $value, $lang): Entity
    {
        $this->getSnapshot($lang)->set($key, $value);

        return $this;
    }

    /**
     * Returns TRUE if entity has key, FALSE otherwise.
     * If entity was deleted then will return FALSE.
     *
     * @param string $key
     * @param string $lang
     */
    public function has($key, $lang): bool
    {
        if ($this->getSnapshot($lang)->wasDeleted($key)) {
            return false;
        }

        return $this->getSnapshot($lang)->has($key);
    }

    /**
     * Delete a key from an Entity.
     *
     * @param string $key
     * @param string $lang
     */
    public function del($key, $lang): void
    {
        $this->getSnapshot($lang)->del($key);
    }

    /**
     * Return all the data associated with this Entity.
     *
     * @param string $lang
     */
    public function all($lang): array
    {
        return $this->getSnapshot($lang)->all();
    }

    /**
     * Return the keys associated with this Entity.
     *
     * @param string $lang
     */
    public function keys($lang): array
    {
        return $this->getSnapshot($lang)->keys();
    }

    /**
     * Returns TRUE if key was deleted from Entity, FALSE otherwise.
     *
     * @param string      $key
     * @param string|null $lang
     */
    public function wasDeleted($key, $lang): bool
    {
        return $this->getSnapshot($lang)->wasDeleted($key);
    }

    /**
     * Returns array of keys deleted from this entity.
     *
     * @param string $lang
     */
    public function getDeletedKeys($lang): array
    {
        return $this->getSnapshot($lang)->getDeletedKeys();
    }

    /**
     * Get the snapshot associated with a language.
     *
     * @param string $lang
     *
     * @throws WanderlusterException
     */
    protected function getSnapshot($lang): Snapshot
    {
        if (!$lang) {
            throw new WanderlusterException(ErrorMessages::ENTITY_LANGUAGE_NOT_SET);
        }

        if (!isset($this->snapshots[$lang])) {
            $this->snapshots[$lang] = new Snapshot($lang);
        }

        return $this->snapshots[$lang];
    }
}
