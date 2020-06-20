<?php

declare(strict_types=1);

namespace App\Persistence;

use App\DataModel\Attributes\Attributes;
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

class AttributeManager
{
    /**
     * @throws WanderlusterException
     */
    public function getDataType(string $attr): DataTypeInterface
    {
        switch ($attr) {
            case Attributes::CORE_FILE_MIME_TYPE:
                return new MimeType();
            case Attributes::CORE_FILE_SIZE:
                return new FileSizeType();
            case Attributes::CORE_FILE_URL:
                return new UrlType();
            case Attributes::CORE_TEST_STRING:
                return new StringType();
            case Attributes::CORE_TEST_STRING_2:
                return new StringType();
            case Attributes::CORE_TEST_STRING_3:
                return new StringType();
            case Attributes::CORE_TEST_EMAIL:
                return new EmailType();
            case Attributes::CORE_TEST_MIME_TYPE:
                return new MimeType();
            case Attributes::CORE_TEST_TRANSLATION:
                return new TranslationType();
            case Attributes::CORE_TEST_URL:
                return new UrlType();
            case Attributes::CORE_TEST_BOOLEAN:
                return new BooleanType();
            case Attributes::CORE_TEST_DATE_TIME:
                return new DateTimeType();
            case Attributes::CORE_TEST_NUM:
                return new NumericType();
            case Attributes::CORE_TEST_INT:
                return new IntegerType();
            default:
                throw new WanderlusterException(sprintf(ErrorMessages::UNKNOWN_ATTRIBUTE_NAME, $attr));
        }
    }
}
