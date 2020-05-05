<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\Serializer\SerializableInterface;
use App\Security\User;
use DateTimeImmutable;

class Snapshot implements SerializableInterface
{
    /**
     * @var string|null
     */
    protected $lang = null;

    /**
     * @var SnapshotId|null
     */
    protected $snapshotId = null;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt = null;

    /**
     * @var User
     */
    protected $createdBy = null;

    /**
     * @var string[]|null[]
     */
    protected $attributes = [];

    /**
     * Snapshot constructor.
     *
     * @param string $lang
     */
    public function __construct($lang, array $data = [])
    {
        $this->lang = $lang;
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get the language for this snapshot.
     */
    public function getLanguage(): ?string
    {
        return $this->lang;
    }

    /**
     * Set the value of an attribute.
     *
     * @param string $key
     * @param mixed|null $value
     */
    public function set($key, $value): void
    {
        $key = (string)$key;
        if (!is_null($value)) {
            $value = (string)$value;
        }

        $this->attributes[$key] = $value;
        ksort($this->attributes);
    }

    /**
     * Get the value of an attribute.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * @param string $key
     */
    public function del($key): void
    {
        $this->set($key, null);
    }

    /**
     * Check if attribute exists.  Return true if exists, false otherwise.
     *
     * @param string $key
     */
    public function has($key): bool
    {
        if (!array_key_exists($key, $this->attributes)) {
            return false;
        }

        $value = $this->attributes[$key];

        if (is_null($value)) {
            return false;
        }

        return true;
    }

    /**
     * Return the keys as an array.
     * Filters out any NULL values.
     *
     * @return string[]
     */
    public function keys(): array
    {
        $return = [];
        foreach ($this->attributes as $key => $value) {
            if (!is_null($value)) {
                $return[] = $key;
            }
        }
        ksort($return);

        return $return;
    }

    /**
     * Check if key was deleted.  Returns TRUE if was deleted, FALSE otherwise.
     *
     * @param string $key
     */
    public function wasDeleted($key): bool
    {
        if (!array_key_exists($key, $this->attributes)) {
            return false;
        }

        return is_null($this->attributes[$key]);
    }

    /**
     * Return the keys that were deleted.
     */
    public function getDeletedKeys(): array
    {
        $return = [];
        foreach ($this->attributes as $key => $value) {
            if (is_null($value)) {
                $return[] = $key;
            }
        }
        ksort($return);

        return $return;
    }

    /**
     * Return all the key=>value pairs.
     * Filters out any NULL values.
     */
    public function all(): array
    {
        $return = [];
        foreach ($this->attributes as $key => $value) {
            if (!is_null($value)) {
                $return[$key] = $value;
            }
        }
        ksort($return);

        return $return;
    }

    public function toArray(): array
    {
        $snapshotId = (string)$this->snapshotId;

        return [
            'type' => 'SNAPSHOT',
            'snapshot_id' => $snapshotId ? $snapshotId : null,
            'data' => $this->all(),
        ];
    }

    public function fromArray(array $data): SerializableInterface
    {
        // TODO: Implement fromArray() method.
        return $this;
    }
}
