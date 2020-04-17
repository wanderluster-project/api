<?php

declare(strict_types=1);

namespace App\Sharding;

class Uuid
{
    protected $uuid;

    /**
     * @var int
     */
    protected $shard;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Uuid constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $uuidParts = explode('-', $uuid);
        $this->shard = (int) $uuidParts[0];
        $this->type = (int) $uuidParts[1];
        $this->identifier =  $uuidParts[2];
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
    public function getEntityType():int
    {
        return $this->type;
    }

    /**
     * Get the Identifier for this object.
     * @return int
     */
    public function getIdentifier():string
    {
        return $this->identifier;
    }

    /**
     * Return the string representation.
     * @return string
     */
    public function asString():string
    {
        return $this->__toString();
    }

    /**
     * Return the string representation.
     * @return string
     */
    public function __toString():string
    {
        return $this->uuid;
    }
}
