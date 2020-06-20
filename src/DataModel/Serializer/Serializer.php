<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\DataModel\Attributes\AttributeManager;
use App\DataModel\Contracts\DataTypeInterface;
use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\DateTimeType;
use App\DataModel\DataType\FileSizeType;
use App\DataModel\DataType\IntegerType;
use App\DataModel\DataType\NumericType;
use App\DataModel\DataType\String\EmailType;
use App\DataModel\DataType\String\MimeType;
use App\DataModel\DataType\String\StringType;
use App\DataModel\DataType\String\TranslationType;
use App\DataModel\DataType\String\UrlType;
use App\DataModel\Entity\Entity;
use App\DataModel\EntityTypeManager;
use App\DataModel\Snapshot\Snapshot;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Serializer
{
    protected LanguageCodes $languageCodes;
    protected EntityTypeManager $entityTypeManager;
    protected AttributeManager $attributeManager;

    /**
     * @var string[]
     */
    protected $objRegistry = [
        Entity::SERIALIZATION_ID => Entity::class,
        Snapshot::SERIALIZATION_ID => Snapshot::class,
        // Data Types
        EmailType::SERIALIZATION_ID => EmailType::class,
        StringType::SERIALIZATION_ID => StringType::class,
        MimeType::SERIALIZATION_ID => MimeType::class,
        TranslationType::SERIALIZATION_ID => TranslationType::class,
        UrlType::SERIALIZATION_ID => UrlType::class,
        BooleanType::SERIALIZATION_ID => BooleanType::class,
        DateTimeType::SERIALIZATION_ID => DateTimeType::class,
        FileSizeType::SERIALIZATION_ID => FileSizeType::class,
        IntegerType::SERIALIZATION_ID => IntegerType::class,
        NumericType::SERIALIZATION_ID => NumericType::class,
    ];

    /**
     * Serializer constructor.
     */
    public function __construct(LanguageCodes $languageCodes, EntityTypeManager $entityTypeManager, AttributeManager $attributeManager)
    {
        $this->languageCodes = $languageCodes;
        $this->entityTypeManager = $entityTypeManager;
        $this->attributeManager = $attributeManager;
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
     * @return Entity|DataTypeInterface|Snapshot
     *
     * @throws WanderlusterException
     */
    public function decode(string $serializedString)
    {
        $data = json_decode($serializedString, true, 25);
        if (json_last_error()) {
            throw new WanderlusterException(sprintf(ErrorMessages::SERIALIZATION_ERROR, 'Error encountered decoding from JSON'));
        }

        $type = isset($data['type']) ? $data['type'] : null;

        if (!$type) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, 'UNKNOWN', 'Missing field: type'));
        }

        if (!isset($this->objRegistry[$type])) {
            throw new WanderlusterException(sprintf(ErrorMessages::DESERIALIZATION_ERROR, 'Invalid type: '.$type));
        }

        return $this->instantiate($type, $data);
    }

    /**
     * Instantiate data type.
     *
     * @return mixed
     */
    public function instantiate(string $type, array $data)
    {
        // Instantiate object
        if (Entity::SERIALIZATION_ID === $type) {
            $obj = new Entity($this, $this->attributeManager);
        } elseif (Snapshot::SERIALIZATION_ID === $type) {
            $obj = new Snapshot($this->attributeManager);
        } else {
            $obj = DataTypeRegistry::instantiate($type);
            $obj->setSerializer($this);
        }
        $obj->fromArray($data);

        return $obj;
    }

    /**
     * Register a data type with the serializer.
     *
     * @return $this
     *
     * @throws WanderlusterException
     */
    public function registerType(string $class, bool $replace = false): self
    {
        $type = new $class();
        $serializationId = $type->getSerializationId();

        if (!$replace && $this->isTypeRegistered($serializationId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_ALREADY_REGISTERED, $serializationId));
        }
        $this->objRegistry[$serializationId] = $class;

        return $this;
    }

    /**
     * Returns TRUE if data type is already registered and false otherwise.
     */
    public function isTypeRegistered(string $serializationId): bool
    {
        return isset($this->objRegistry[$serializationId]);
    }
}
