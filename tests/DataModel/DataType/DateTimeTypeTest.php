<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\DateTimeType;
use App\Exception\WanderlusterException;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DateTimeTypeTest extends TestCase //implements TypeTestInterface
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

    public function testTranslationsException(): void
    {
        // datetime doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new DateTimeType();
        $this->assertEquals(['val' => null, 'type' => 'DATE_TIME', 'ver' => 0], $sut->toArray());

        $sut = new DateTimeType('1/1/2000', ['ver' => 10]);
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME', 'ver' => 10], $sut->toArray());

        $sut = new DateTimeType(new DateTime('1/1/2000'), ['ver' => 50]);
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME', 'ver' => 50], $sut->toArray());

        $sut = new DateTimeType(new DateTimeImmutable('1/1/2000'), ['ver' => 100]);
        $this->assertEquals(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME', 'ver' => 100], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new DateTimeType();
        $sut->fromArray(['val' => null, 'type' => 'DATE_TIME', 'ver' => 0]);
        $this->assertNull($sut->getValue());
        $this->assertEquals(0, $sut->getVersion());

        $sut->fromArray(['val' => '2000-01-01T00:00:00+00:00', 'type' => 'DATE_TIME', 'ver' => 10]);
        $this->assertInstanceOf(DateTimeImmutable::class, $sut->getValue());
        $this->assertEquals(946684800, $sut->getValue()->getTimestamp());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['val' => true]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating DATE_TIME data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'DATE_TIME']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating DATE_TIME data type - Missing Field: val.', $e->getMessage());
        }

        // invalid date string
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'DATE_TIME', 'val' => 'I am invalid', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }

        // invalid value
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'DATE_TIME', 'val' => 3.14, 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }

        // invalid type
        $sut = new DateTimeType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => '1/1/2000', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating DATE_TIME data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new DateTimeType('1/1/2020', ['ver' => 10]);
        $sut2 = new DateTimeType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals('01/01/2020', $sut2->getValue()->format('m/d/Y'));
        $this->assertEquals(10, $sut2->getVersion());
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

    public function testSetGetVersion(): void
    {
        $sut = new DateTimeType();
        $this->assertEquals(0, $sut->getVersion());
        $sut->setVersion(10);
        $this->assertEquals(10, $sut->getVersion());

        try {
            $sut->setVersion(-1);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid version: -1.', $e->getMessage());
        }
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
            $sut = new DateTimeType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new DateTimeType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new DateTimeType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new DateTimeType('1/1/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType('1/2/2020', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/01/2020', $sut->getValue()->format('m/d/Y'));

        // Merging same version
        $sut = new DateTimeType('1/1/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType('1/2/2020', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/02/2020', $sut->getValue()->format('m/d/Y'));

        $sut = new DateTimeType('1/2/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType('1/1/2020', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/02/2020', $sut->getValue()->format('m/d/Y'));

        // Merging greater version
        $sut = new DateTimeType('1/2/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType('1/1/2020', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('01/01/2020', $sut->getValue()->format('m/d/Y'));
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new DateTimeType('1/1/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/01/2020', $sut->getValue()->format('m/d/Y'));

        $sut = new DateTimeType(null, ['ver' => 10]);
        $sut->merge(new DateTimeType('1/1/2020', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new DateTimeType(null, ['ver' => 10]);
        $sut->merge(new DateTimeType('01/02/2020', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/02/2020', $sut->getValue()->format('m/d/Y'));

        $sut = new DateTimeType('1/1/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('01/01/2020', $sut->getValue()->format('m/d/Y'));

        $sut = new DateTimeType(null, ['ver' => 10]);
        $sut->merge(new DateTimeType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging greater version
        $sut = new DateTimeType('1/2/2020', ['ver' => 10]);
        $sut->merge(new DateTimeType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new DateTimeType(null, ['ver' => 10]);
        $sut->merge(new DateTimeType('1/2/2020', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('01/02/2020', $sut->getValue()->format('m/d/Y'));
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new DateTimeType('1/1/2020');
        $obj2 = new DateTimeType('1/1/2019');

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new DateTimeType('1/1/2020');
        $obj2 = new DateTimeType('1/1/2019');
        $obj3 = new DateTimeType('1/1/2020');

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new DateTimeType('1/1/2020', ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with DATE_TIME.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new DateTimeType();
        $this->assertTrue($sut->isValidValue(new DateTime('1/1/2000')));
        $this->assertTrue($sut->isValidValue('1/1/2000'));
        $this->assertFalse($sut->isValidValue('I am invalid'));
    }

    public function testIsValidNull(): void
    {
        $sut = new DateTimeType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new DateTimeType();
        $this->assertEquals('01/01/2000', $sut->coerce(new DateTime('1/1/2000'))->format('m/d/Y'));
        $this->assertEquals('01/01/2000', $sut->coerce('1/1/2000')->format('m/d/Y'));
    }

    public function testCoerceNull(): void
    {
        $sut = new DateTimeType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new DateTimeType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }

        try {
            $sut = new DateTimeType('1999-04-31');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to DATE_TIME data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new DateTimeType();
        $this->assertNull($sut->getSerializedValue());
        $sut->setValue('1/1/2000');
        $this->assertEquals('2000-01-01T00:00:00+00:00', $sut->getSerializedValue());
    }
}
