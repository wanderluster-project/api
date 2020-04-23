<?php

declare(strict_types=1);

namespace App\DataModel\Snapshot;

use App\DataModel\User;
use DateTimeImmutable;

class Snapshot
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * @var SnapshotId|null
     */
    protected $snapshotId;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt = null;

    /**
     * @var User
     */
    protected $createdBy = null;

    /**
     * @var int
     */
    protected $version = 0;

    /**
     * @var string[]
     */
    protected $attributes = [];

    /**
     * Snapshot constructor.
     *
     * @param string $lang
     */
    public function __construct($lang, SnapshotId $snapshotId = null)
    {
        $this->lang = $lang;
        $this->snapshotId = $snapshotId;
    }

    /**
     * Get the language for this snapshot.
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * Get the version of this snapshot.
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set the value of an attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): void
    {
        $key = (string) $key;
        $value = (string) $value;

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
        if ($this->has($key)) {
            unset($this->attributes[$key]);
        }
    }

    /**
     * Check if attribute exists.  Return true if exists, false otherwise.
     *
     * @param string $key
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Return the keys as an array.
     *
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->attributes);
    }

    /**
     * Get all the attributes.
     */
    public function all(): array
    {
        return $this->attributes;
    }
}
