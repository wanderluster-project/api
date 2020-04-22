<?php

declare(strict_types=1);

namespace App\EntityManager\Persistence;

class Shard
{
    /**
     * @var int
     */
    protected $shardId;

    /**
     * Shard constructor.
     */
    public function __construct(int $shardId)
    {
        $this->shardId = $shardId;
    }

    public function getShardId(): int
    {
        return $this->shardId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->shardId;
    }
}
