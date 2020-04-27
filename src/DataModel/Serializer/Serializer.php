<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
use App\DataModel\Snapshot\SnapshotId;
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
     * Serializer constructor.
     */
    public function __construct(EntityUtilites $entityUtilites)
    {
        $this->entityUtilites = $entityUtilites;
    }

    /**
     * @param mixed $val
     *
     * @throws WanderlusterException
     */
    public function encode($val): string
    {
        if (is_scalar($val)) {
            return (string) $val;
        } elseif (is_array($val)) {
            return $this->encodeArray($val);
        } elseif (is_object($val)) {
            return $this->encodeObject($val);
        }
        throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'UNKNOWN'));
    }

    /**
     * @param string $serializedString
     * @param string $type
     *
     * @return Entity|EntityId|SnapshotId
     *
     * @throws WanderlusterException
     */
    public function decode($serializedString, $type)
    {
        return $this->decodeObject($serializedString, $type);
    }

    protected function encodeArray(array $arr): string
    {
        if ($this->isAssocArray($arr)) {
            $items = [];
            foreach ($arr as $key => $value) {
                $encodedKey = $this->encode($key);
                $encodedValue = $this->encode($value);
                $items[] = '"'.$encodedKey.'":"'.$encodedValue.'"';
            }

            return '{'.implode($items, ',').'}';
        } else {
            $items = [];
            foreach ($arr as $value) {
                $encodedValue = $this->encode($value);
                $items[] = $encodedValue;
            }

            return '['.implode($items, ',').']';
        }
    }

    /**
     * Check if associative array or integer indexed array.
     * Will return TRUE if associative array.
     */
    protected function isAssocArray(array $arr): bool
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param object $obj
     */
    protected function encodeObject($obj): string
    {
        $class = get_class($obj);
        switch ($class) {
            case EntityId::class:
                return (string) $obj;
            case SnapshotId::class:
                return (string) $obj;
            case Entity::class:
                /**
                 * @var Entity $obj
                 */
                $entityId = $obj->getEntityId();
                if (is_null($entityId)) {
                    $encodedEntityId = null;
                } else {
                    $encodedEntityId = $this->encodeObject($entityId);
                }

                return json_encode([
                    'id' => $encodedEntityId,
                    'lang' => $obj->getLang(),
                    'data' => $obj->all(),
                ]);
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, $class));
        }
    }

    /**
     * @param string $string
     * @param string $class
     *
     * @return EntityId|SnapshotId|Entity
     *
     * @throws WanderlusterException
     */
    protected function decodeObject($string, $class)
    {
        switch ($class) {
            case EntityId::class:
                return $this->decodeEntityId($string);
            case SnapshotId::class:
                return $this->decodeSnapshotId($string);
            case Entity::class:
               return $this->decodeEntity($string);
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, $class));
        }
    }

    /**
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeEntityId($string): EntityId
    {
        return new EntityId($string);
    }

    /**
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeSnapshotId($string): SnapshotId
    {
        return new SnapshotId($string);
    }

    /**
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeEntity($string): Entity
    {
        $jsonData = json_decode($string, true);
        $entityId = null;

        // decode entity id
        if (!array_key_exists('id', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, Entity::class));
        }
        if (!is_null($jsonData['id'])) {
            $entityId = $this->decodeEntityId($jsonData['id']);
        }

        // decode language
        if (!array_key_exists('lang', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, Entity::class));
        }
        $lang = $jsonData['lang'];

        // decode data values
        if (!array_key_exists('data', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, Entity::class));
        }
        $data = $jsonData['data'];

        $entity = new Entity($data, $lang);
        if ($entityId) {
            $this->entityUtilites->setEntityId($entity, $entityId);
        }

        return $entity;
    }
}
