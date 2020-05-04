<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\DateTimeType;
use App\Exception\TypeError;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DateTimeTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new DateTimeType();
        $this->assertNull($sut->getValue());
    }

    public function testIsNull(): void
    {
        // empty
        $sut = new DateTimeType();
        $this->assertTrue($sut->isNull());

        // passing date string
        $sut = new DateTimeType('1/1/2000');
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        // passing date string
        $sut = new DateTimeType('1/1/2000');
        $this->assertFalse($sut->isNull());

        // passing DateTime
        $sut = new DateTimeType(new DateTime('1/1/2000'));
        $this->assertFalse($sut->isNull());

        // passing DateTimeImmutable
        $sut = new DateTimeType(new DateTimeImmutable('1/1/2000'));
        $this->assertFalse($sut->isNull());
    }

    public function testTranslations(): void
    {
        // datetime doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new DateTimeType();
        $this->assertEquals(['val' => null, 'type' => 'DATE_TIME'], $sut->toArray());

        $sut = new DateTimeType('1/1/2000');
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME'], $sut->toArray());

        $sut = new DateTimeType(new DateTime('1/1/2000'));
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00',  'type' => 'DATE_TIME'], $sut->toArray());

        $sut = new DateTimeType(new DateTimeImmutable('1/1/2000'));
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00',  'type' => 'DATE_TIME'], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new DateTimeType();
        $sut->fromArray(['val' => null, 'type' => 'DATE_TIME']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME']);
        $this->assertInstanceOf(DateTimeImmutable::class, $sut->getValue());
        $this->assertEquals(946684800, $sut->getValue()->getTimestamp());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['val' => true]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating DATE_TIME data type - Missing Field: type', $e->getMessage());
        }

        // missing value
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'DATE_TIME']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating DATE_TIME data type - Missing Field: val', $e->getMessage());
        }

        // invalid value
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'DATE_TIME', 'val' => 'I am invalid']);
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type - Invalid date string.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new DateTimeType();
        $this->assertNull($sut->getValue());

        $sut->setValue('1/1/2000');
        $this->assertInstanceOf(DateTimeImmutable::class, $sut->getValue());

        $sut->setValue(new DateTime('1/1/2000'));
        $this->assertInstanceOf(DateTimeImmutable::class, $sut->getValue());

        $sut->setValue(new DateTimeImmutable('1/1/2000'));
        $this->assertInstanceOf(DateTimeImmutable::class, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new DateTimeType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new DateTimeType('I am a string');
            $this->fail('Exception not thrown');
        } catch (TypeError $e) {
            $this->assertInstanceOf(TypeError::class, $e);
        }

        try {
            $sut = new DateTimeType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type - Invalid date string.', $e->getMessage());
        }
    }
}
