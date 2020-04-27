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
     */
    public function __construct(array $data = [], $lang = null)
    {
        $this->snapshot = new Snapshot($data, $lang);
    }

    /**
     * Get the language of this Entity.
     */
    public function getLang(): ?string
    {
        return $this->snapshot->getLanguage();
    }

    /**
     * Get the EntityId for this Entity.
     */
    public function getEntityId(): ?EntityId
    {
        return $this->entityId;
    }

    /**
     * @param string $key
     */
    public function get($key): ?string
    {
        return $this->snapshot->get($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): void
    {
        $this->snapshot->set($key, $value);
    }

    /**
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
     * @param string $key
     */
    public function del($key): void
    {
        $this->snapshot->del($key);
    }

    public function all(): array
    {
        $return = $this->snapshot->all();

        foreach ($this->snapshot->getDeletedKeys() as $deletedKey) {
            if (array_key_exists($deletedKey, $return)) {
                unset($return[$deletedKey]);
            }
        }

        ksort($return);

        return $return;
    }
}
