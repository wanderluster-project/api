<?php

declare(strict_types=1);

namespace App\Sharding;

class ShardCoordinator
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * ShardCoordinator constructor.
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Simple algorithm.  This can be expanded in the future.
     * @return int
     */
    public function getAvailableShard(): Shard
    {
        $shardId = rand($this->min, $this->max);
        return new Shard($shardId);
    }

    public function isValidShard(Shard $shard): bool
    {
        $shardId = $shard->getShardId();
        return $shardId>= $this->min && $shardId <= $this->max;
    }
}
