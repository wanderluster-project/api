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
        if (is_null($val)) {
            return 'null';
        } elseif (is_scalar($val)) {
            if (true === $val) {
                return 'true';
            } elseif (false === $val) {
                return 'false';
            } elseif (is_int($val)) {
                return (string) $val;
            } elseif (is_float($val)) {
                return (string) $val;
            } elseif (is_string($val)) {
                return (string) $val;
            }
        } elseif (is_array($val)) {
            return $this->encodeArray($val);
        } elseif (is_object($val)) {
            return $this->encodeObject($val);
        }
        throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Invalid data type'));
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
                return $this->decodeEntityId($serializedString);
            case SnapshotId::class:
                return $this->decodeSnapshotId($serializedString);
            case Entity::class:
                return $this->decodeEntity($serializedString);
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Invalid class: '.$class));
        }
    }

    /**
     * Convert an array into a string representation.
     * If indexed  : ["a","b"]
     * If associative : {"foo":"bar"}.
     *
     * @throws WanderlusterException
     */
    protected function encodeArray(array $arr): string
    {
        if ($this->isAssocArray($arr)) {
            $items = [];
            foreach ($arr as $key => $value) {
                $encodedKey = $this->encode($key);
                if (is_string($value)) {
                    $encodedValue = '"'.$this->encode($value).'"';
                } else {
                    $encodedValue = $this->encode($value);
                }
                $items[] = '"'.$encodedKey.'":'.$encodedValue;
            }

            return '{'.implode($items, ',').'}';
        } else {
            $items = [];
            foreach ($arr as $value) {
                if (is_string($value)) {
                    $encodedValue = '"'.$this->encode($value).'"';
                } else {
                    $encodedValue = $this->encode($value);
                }
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
     * Encode an object into a string representation.
     *
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

                $data = [];
                $languages = $obj->getLanguages();
                foreach ($languages as $language) {
                    $all = $obj->all($language);
                    if (!$all) {
                        $all = new \stdClass();
                    }
                    $data[$language] = $all;
                }
                if (!$data) {
                    $data = new \stdClass();
                }

                return json_encode([
                    'id' => $encodedEntityId,
                    'type' => $obj->getEntityType(),
                    'lang' => $obj->getLanguage(),
                    'data' => $data,
                ]);
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Invalid class: '.$class));
        }
    }

    /**
     * Decode string representation into EntityId.
     *
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeEntityId($string): EntityId
    {
        return new EntityId($string);
    }

    /**
     * Decode string representation into SnapshotId.
     *
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeSnapshotId($string): SnapshotId
    {
        return new SnapshotId($string);
    }

    /**
     * Decode string representation into Entity.
     *
     * @param string $string
     *
     * @throws WanderlusterException
     */
    protected function decodeEntity($string): Entity
    {
        $jsonData = json_decode($string, true);
        if (is_null($jsonData)) {
            throw new WanderlusterException(ErrorMessages::INVALID_JSON);
        }

        $entityId = null;

        // decode entity id
        if (!array_key_exists('id', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Missing parameter: id'));
        }
        if (!is_null($jsonData['id'])) {
            $entityId = $this->decodeEntityId($jsonData['id']);
        }

        // decode language
        if (!array_key_exists('lang', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Missing parameter: lang'));
        }
        $lang = $jsonData['lang'];

        // decode entity type
        if (!array_key_exists('type', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Missing parameter: type'));
        }
        $entityType = $jsonData['type'];

        if (!$this->entityTypeManager->isValidType($entityType)) {
            throw new WanderlusterException(sprintf(ErrorMessages::INVALID_ENTITY_TYPE, $entityType));
        }

        // decode data values
        if (!array_key_exists('data', $jsonData)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Missing parameter: data'));
        }
        $data = $jsonData['data'];
        if (!is_array($data)) {
            throw new WanderlusterException(ErrorMessages::INVALID_ENTITY_DATA);
        }

        $entity = new Entity($lang, $entityType);
        if ($entityId) {
            $this->entityUtilites->setEntityId($entity, $entityId);
        }
        foreach ($data as $lang => $langData) {
            if (!is_array($langData)) {
                throw new WanderlusterException(ErrorMessages::INVALID_ENTITY_DATA);
            }
            if (!$this->languageCodes->isValidLanguageCode($lang)) {
                throw new WanderlusterException(ErrorMessages::INVALID_LANGUAGE_CODE);
            }
            foreach ($langData as $key => $value) {
                $entity->set($key, $value, $lang);
            }
        }

        // set language to null so user has to specify it
        $entity->setLanguage(null);

        return $entity;
    }
}
