<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Attributes;

use App\DataModel\Attributes\Attributes;
use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\DateTimeType;
use App\DataModel\DataType\IntegerType;
use App\DataModel\DataType\NumericType;
use App\DataModel\DataType\String\EmailType;
use App\DataModel\DataType\String\MimeType;
use App\DataModel\DataType\String\StringType;
use App\DataModel\DataType\String\TranslationType;
use App\DataModel\DataType\String\UrlType;
use App\Exception\WanderlusterException;
use App\Persistence\AttributeManager;
use PHPUnit\Framework\TestCase;

class AttributeManagerTest extends TestCase
{
    public function testGetDataType(): void
    {
        $sut = new AttributeManager();
        $this->assertInstanceOf(EmailType::class, $sut->getDataType(Attributes::CORE_TEST_EMAIL));
        $this->assertInstanceOf(StringType::class, $sut->getDataType(Attributes::CORE_TEST_STRING));
        $this->assertInstanceOf(MimeType::class, $sut->getDataType(Attributes::CORE_TEST_MIME_TYPE));
        $this->assertInstanceOf(TranslationType::class, $sut->getDataType(Attributes::CORE_TEST_TRANSLATION));
        $this->assertInstanceOf(UrlType::class, $sut->getDataType(Attributes::CORE_TEST_URL));
        $this->assertInstanceOf(BooleanType::class, $sut->getDataType(Attributes::CORE_TEST_BOOLEAN));
        $this->assertInstanceOf(DateTimeType::class, $sut->getDataType(Attributes::CORE_TEST_DATE_TIME));
        $this->assertInstanceOf(IntegerType::class, $sut->getDataType(Attributes::CORE_TEST_INT));
        $this->assertInstanceOf(NumericType::class, $sut->getDataType(Attributes::CORE_TEST_NUM));

        // test exception
        try {
            $sut->getDataType('foo');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Unknown attribute - foo', $e->getMessage());
        }
    }
}
