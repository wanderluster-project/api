<?php

declare(strict_types=1);

namespace App\Sharding;

class Uuid
{
    public $uuid;

    /**
     * @var int
     */
    public $shard;

    /**
     * @var int
     */
    public $type;

    /**
     * @var int
     */
    public $identifier;

    /**
     * Uuid constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $uuidParts = explode('-', $uuid);
        $this->shard = hexdec($uuidParts[0]);
        $this->type = hexdec($uuidParts[1]);
        $this->identifier = hexdec($uuidParts[2]);
    }

    /**
     * Get the shard where this object is stored.
     * @return int
     */
    public function getShard():int
    {
        return $this->shard;
    }

    /**
     * Get the type of this object.
     * @return int
     */
    public function getType():int
    {
        return $this->type;
    }

    /**
     * Get the Identifier for this object.
     * @return int
     */
    public function getIdentifier():int
    {
        return $this->identifier;
    }
}
