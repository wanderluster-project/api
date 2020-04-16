<?php

namespace App\Sharding;

class Shard
{
    /**
     * @var int
     */
    protected $shardId;

    /**
     * Shard constructor.
     * @param int $shardId
     */
    public function __construct(int $shardId)
    {
        $this->shardId = $shardId;
    }

    /**
     * @return int
     */
    public function getShardId():int
    {
        return $this->shardId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->shardId;
    }
}
