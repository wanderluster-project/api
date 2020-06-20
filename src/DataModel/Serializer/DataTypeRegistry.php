<?php

declare(strict_types=1);

namespace App\DataModel\Serializer;

use App\DataModel\Contracts\DataTypeInterface;
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
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class DataTypeRegistry
{
    /**
     * @var string[]
     */
    protected static $registry = [
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

        if (!$replace && self::isTypeRegistered($serializationId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_ALREADY_REGISTERED, $serializationId));
        }
        self::$registry[$serializationId] = $class;

        return $this;
    }

    /**
     * Returns TRUE if data type is already registered and false otherwise.
     */
    public static function isTypeRegistered(string $serializationId): bool
    {
        return isset(self::$registry[$serializationId]);
    }

    /**
     * Instantiate data type.
     *
     * @return mixed
     */
    public static function instantiate(string $serializationId): DataTypeInterface
    {
        if (self::isTypeRegistered($serializationId)) {
            throw new WanderlusterException(sprintf(ErrorMessages::DATA_TYPE_UNKOWN, $serializationId));
        }
        $class = self::$registry[$serializationId];
        $obj = new $class();

        return $obj;
    }
}
