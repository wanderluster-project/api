<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Snapshot\SnapshotId;
use App\DataModel\Translation\LanguageCodes;
use App\EntityManager\EntityTypeManager;
use App\EntityManager\EntityUtilites;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Serializer
{
    /**
     * @var EntityUtilites
     */
    protected $entityUtilites;

    /**
     * @var LanguageCodes
     */
    protected $languageCodes;

    /**
     * @var EntityTypeManager
     */
    protected $entityTypeManager;

    /**
     * Serializer constructor.
     */
    public function __construct(EntityUtilites $entityUtilites, LanguageCodes $languageCodes, EntityTypeManager $entityTypeManager)
    {
        $this->entityUtilites = $entityUtilites;
        $this->languageCodes = $languageCodes;
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * Encode an object into string representation.
     *
     * @param mixed $val
     *
     * @throws WanderlusterException
     */
    public function encode($val): string
    {
        if (is_object($val)) {
            if ($val instanceof SerializableInterface) {
                return json_encode($val->toArray());
            } elseif ($val instanceof EntityId) {
                return (string) $val;
            } elseif ($val instanceof SnapshotId) {
                return (string) $val;
            } else {
                throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Invalid data type'));
            }
        }

        $json = json_encode($val);
        if (json_last_error()) {
            throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Invalid data type'));
        }

        return $json;
    }

    /**
     * Decode a string representation into an Object.
     *
     * @param string $serializedString
     * @param string $class
     *
     * @return Entity|EntityId|SnapshotId
     *
     * @throws WanderlusterException
     */
    public function decode($serializedString, $class)
    {
        switch ($class) {
            case EntityId::class:
                return new EntityId($serializedString);
            case SnapshotId::class:
                return new SnapshotId($serializedString);
            case Entity::class:
                $data = json_decode($serializedString, true);
                $entity = new Entity();
                $entity->fromArray($data);

                return $entity;
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Invalid class: '.$class));
        }
    }
}
