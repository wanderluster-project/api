<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\NumericType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class NumericTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isNull());

        $sut = new NumericType(150);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        // integer
        $sut = new NumericType(150);
        $this->assertEquals(150, $sut->getValue());

        // float
        $sut = new NumericType(3.14);
        $this->assertEquals(3.14, $sut->getValue());
    }

    public function testTranslations(): void
    {
        // numeric doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // numeric doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        // null
        $sut = new NumericType();
        $this->assertEquals(['type' => 'NUM', 'val' => null, 'ver' => 0], $sut->toArray());

        // integer
        $sut = new NumericType(150, ['ver' => 10]);
        $this->assertEquals(['type' => 'NUM', 'val' => 150, 'ver' => 10], $sut->toArray());

        // float
        $sut = new NumericType(3.14, ['ver' => 10]);
        $this->assertEquals(['type' => 'NUM', 'val' => 3.14, 'ver' => 10], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new NumericType();
        $sut->fromArray(['val' => null, 'type' => 'NUM', 'ver' => 0]);
        $this->assertNull($sut->getValue());
        $this->assertEquals(0, $sut->getVersion());

        $sut->fromArray(['type' => 'NUM', 'val' => 3.14, 'ver' => 10]);
        $this->assertEquals(3.14, $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new NumericType();
        try {
            $sut->fromArray(['val' => 140]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'NUM']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'NUM', 'val' => 3.14]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'NUM', 'val' => 'I AM INVALID', 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to NUM data type.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 3.14, 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new NumericType(3.14, ['ver' => 10]);
        $sut2 = new NumericType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals(3.14, $sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->getValue());

        $sut->setValue(150);
        $this->assertEquals(150, $sut->getValue());

        $sut->setValue(-1);
        $this->assertEquals(-1, $sut->getValue());

        $sut->setValue(3.14);
        $this->assertEquals(3.14, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new NumericType();
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

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new NumericType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to NUM data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new NumericType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to NUM data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new NumericType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new NumericType(25.0, ['ver' => 10]);
        $sut->merge(new NumericType(15.0, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        // Merging same version
        $sut = new NumericType(25.0, ['ver' => 10]);
        $sut->merge(new NumericType(15.0, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        $sut = new NumericType(15.0, ['ver' => 10]);
        $sut->merge(new NumericType(25.0, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        // Merging greater version
        $sut = new NumericType(35.0, ['ver' => 10]);
        $sut->merge(new NumericType(25.0, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new NumericType(25.0, ['ver' => 10]);
        $sut->merge(new NumericType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        $sut = new NumericType(null, ['ver' => 10]);
        $sut->merge(new NumericType(15.0, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new NumericType(25.0, ['ver' => 10]);
        $sut->merge(new NumericType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        $sut = new NumericType(null, ['ver' => 10]);
        $sut->merge(new NumericType(25.0, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());

        // Merging greater version
        $sut = new NumericType(35.0, ['ver' => 10]);
        $sut->merge(new NumericType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new NumericType(null, ['ver' => 10]);
        $sut->merge(new NumericType(25.0, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame(25.0, $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new NumericType(3.14);
        $obj2 = new NumericType(2.71);

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new NumericType(3.14);
        $obj2 = new NumericType(2.71);
        $obj3 = new NumericType(3.14);

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new NumericType(3.14, ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with NUM.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isValidValue(3.14));
        $this->assertTrue($sut->isValidValue(150));
        $this->assertFalse($sut->isValidValue('Invalid email'));
    }

    public function testIsValidNull(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new NumericType();
        $this->assertEquals(3.14, $sut->coerce(3.14));
        $this->assertEquals(3, $sut->coerce(3));
    }

    public function testCoerceNull(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new NumericType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to NUM data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->getSerializedValue());
        $sut->setValue(3.14);
        $this->assertEquals(3.14, $sut->getSerializedValue());
    }
}
