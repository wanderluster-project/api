<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\DataModel\Entity\Entity;
use App\DataModel\Entity\EntityId;
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
     * @throws WanderlusterException
     */
    public function encode(SerializableInterface $val): string
    {
        $json = json_encode($val->toArray());
        if (json_last_error()) {
            throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Error encountered encoding to JSON'));
        }

        return $json;
    }

    /**
     * Decode a string representation into an Object.
     *
     * @param string $serializedString
     *
     * @return Entity|EntityId
     *
     * @throws WanderlusterException
     */
    public function decode($serializedString)
    {
        $data = json_decode($serializedString, true);
        if (json_last_error()) {
            throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Error encountered decoding from JSON'));
        }

        $type = isset($data['type']) ? $data['type'] : null;

        if (!$type) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, 'UNKNOWN', 'Missing field: type'));
        }

        if ('ENTITY' === $type) {
            $ret = new Entity();
            $ret->fromArray($data);
        } else {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Invalid type: '.$type));
        }

        return $ret;
    }
}
