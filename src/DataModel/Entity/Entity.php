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
     * @var int
     */
    protected $entityType = null;

    /**
     * @var Snapshot[]
     */
    protected $snapshots;

    /**
     * @var string
     */
    protected $currentLang;

    /**
     * Entity constructor.
     *
     * @param string $lang
     */
    public function __construct($lang, int $entityType, array $data = [])
    {
        $this->currentLang = $lang;
        $this->entityType = $entityType;
        $this->setSnapshot($this->currentLang, new Snapshot($lang, $data));
    }

    /**
     * Get the language of this Entity.
     */
    public function getLang(): ?string
    {
        return $this->currentLang;
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
     */
    public function get($key): ?string
    {
        return $this->getSnapshot()->get($key);
    }

    /**
     * Set the value associated with a key.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): void
    {
        $this->getSnapshot()->set($key, $value);
    }

    /**
     * Returns TRUE if entity has key, FALSE otherwise.
     * If entity was deleted then will return FALSE.
     *
     * @param string $key
     */
    public function has($key): bool
    {
        if ($this->getSnapshot()->wasDeleted($key)) {
            return false;
        }

        return $this->getSnapshot()->has($key);
    }

    /**
     * Delete a key from an Entity.
     *
     * @param string $key
     */
    public function del($key): void
    {
        $this->getSnapshot()->del($key);
    }

    /**
     * Return all the data associated with this Entity.
     */
    public function all(): array
    {
        return $this->getSnapshot()->all();
    }

    /**
     * Return the keys associaated with this Entity.
     */
    public function keys(): array
    {
        return $this->getSnapshot()->keys();
    }

    /**
     * Returns TRUE if key was deleted from Entity, FALSE otherwise.
     *
     * @param string $key
     */
    public function wasDeleted($key): bool
    {
        return $this->getSnapshot()->wasDeleted($key);
    }

    /**
     * Returns array of keys deleted from this entity.
     */
    public function getDeletedKeys(): array
    {
        return $this->getSnapshot()->getDeletedKeys();
    }

    /**
     * Get the snapshot associated with a language.
     *
     * @param string|null $lang
     */
    protected function getSnapshot($lang = null): Snapshot
    {
        $lang = $lang ? $lang : $this->currentLang;

        return $this->snapshots[$lang];
    }

    /**
     * Set the snapshot associated with a language.
     *
     * @param string $lang
     */
    protected function setSnapshot($lang, Snapshot $snapshot): void
    {
        $this->snapshots[$lang] = $snapshot;
    }
}
