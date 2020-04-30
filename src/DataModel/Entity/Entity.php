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
     * @var string
     */
    protected $currentLang;

    /**
     * Entity constructor.
     *
     * @param string $lang
     */
    public function __construct($lang, int $entityType)
    {
        $this->setLanguage($lang);
        $this->entityType = $entityType;
    }

    /**
     * Get the language of this Entity.
     */
    public function getLanguage(): ?string
    {
        return $this->currentLang;
    }

    /**
     * @param string|null $lang
     */
    public function setLanguage($lang): void
    {
        $this->currentLang = $lang;
        if (!$lang) {
            return;
        }

        if (!isset($this->snapshots[$lang])) {
            $this->snapshots[$lang] = new Snapshot($lang);
        }
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
     * @param string      $key
     * @param string|null $lang
     */
    public function get($key, $lang = null): ?string
    {
        return $this->getSnapshot($lang)->get($key);
    }

    /**
     * Set the value associated with a key.
     *
     * @param string      $key
     * @param mixed       $value
     * @param string|null $lang
     */
    public function set($key, $value, $lang = null): Entity
    {
        $this->getSnapshot($lang)->set($key, $value);

        return $this;
    }

    /**
     * Returns TRUE if entity has key, FALSE otherwise.
     * If entity was deleted then will return FALSE.
     *
     * @param string      $key
     * @param string|null $lang
     */
    public function has($key, $lang = null): bool
    {
        if ($this->getSnapshot($lang)->wasDeleted($key)) {
            return false;
        }

        return $this->getSnapshot()->has($key);
    }

    /**
     * Delete a key from an Entity.
     *
     * @param string      $key
     * @param string|null $lang
     */
    public function del($key, $lang = null): void
    {
        $this->getSnapshot($lang)->del($key);
    }

    /**
     * Return all the data associated with this Entity.
     *
     * @param string|null $lang
     */
    public function all($lang = null): array
    {
        return $this->getSnapshot($lang)->all();
    }

    /**
     * Return the keys associated with this Entity.
     *
     * @param string|null $lang
     */
    public function keys($lang = null): array
    {
        return $this->getSnapshot($lang)->keys();
    }

    /**
     * Returns TRUE if key was deleted from Entity, FALSE otherwise.
     *
     * @param string      $key
     * @param string|null $lang
     */
    public function wasDeleted($key, $lang = null): bool
    {
        return $this->getSnapshot($lang)->wasDeleted($key);
    }

    /**
     * Returns array of keys deleted from this entity.
     *
     * @param string|null $lang
     */
    public function getDeletedKeys($lang = null): array
    {
        return $this->getSnapshot($lang)->getDeletedKeys();
    }

    /**
     * Get the snapshot associated with a language.
     *
     * @param string|null $lang
     *
     * @throws WanderlusterException
     */
    protected function getSnapshot($lang = null): Snapshot
    {
        $lang = $lang ? $lang : $this->currentLang;

        if (!$lang) {
            throw new WanderlusterException(ErrorMessages::ENTITY_LANGUAGE_NOT_SET);
        }

        if (!isset($this->snapshots[$lang])) {
            $this->snapshots[$lang] = new Snapshot($lang);
        }

        return $this->snapshots[$lang];
    }
}
