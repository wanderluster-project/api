<?php

declare(strict_types=1);

namespace App\DataModel\Entity;

use App\DataModel\Snapshot\Snapshot;

class Entity
{
    /**
     * @var string|null
     */
    protected $lang;

    /**
     * @var EntityId|null
     */
    protected $entityId = null;

    /**
     * @var Snapshot|null
     */
    protected $previousSnapshot;

    /**
     * @var Snapshot
     */
    protected $currentSnapshot;

    /**
     * Entity constructor.
     *
     * @param string|null $lang
     */
    public function __construct(EntityId $entityId = null, Snapshot $previousSnapshot = null, $lang = null)
    {
        $this->entityId = $entityId;
        $this->previousSnapshot = $previousSnapshot;
        $this->lang = $lang;
        $this->currentSnapshot = new Snapshot($lang);
    }

    /**
     * Get the language of this Entity.
     */
    public function getLang(): ?string
    {
        return $this->lang;
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
        $curValue = $this->currentSnapshot->get($key);

        if (!is_null($curValue)) {
            return $curValue;
        }

        if ($this->previousSnapshot) {
            return $this->previousSnapshot->get($key);
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): void
    {
        $this->currentSnapshot->set($key, $value);
    }

    /**
     * @param string $key
     */
    public function has($key): bool
    {
        if ($this->currentSnapshot->wasDeleted($key)) {
            return false;
        } elseif ($this->currentSnapshot->has($key)) {
            return true;
        } elseif ($this->previousSnapshot) {
            return $this->previousSnapshot->has($key);
        }

        return false;
    }

    /**
     * @param string $key
     */
    public function del($key): void
    {
        $this->currentSnapshot->del($key);
    }
}
