<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\IntegerType;
use App\DataModel\DataType\String\EmailType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class IntegerTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isNull());

        $sut = new IntegerType(150);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new IntegerType(5);
        $this->assertEquals(5, $sut->getValue());
    }

    public function testTranslations(): void
    {
        // integer doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // integer doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        // null
        $sut = new IntegerType();
        $this->assertEquals(['val' => null, 'type' => 'INT', 'ver' => 0], $sut->toArray());

        // negative value
        $sut = new IntegerType(-1, ['ver' => 150]);
        $this->assertEquals(['val' => -1, 'type' => 'INT', 'ver' => 150], $sut->toArray());

        // positive value
        $sut = new IntegerType(150, ['ver' => 150]);
        $this->assertEquals(['val' => 150, 'type' => 'INT', 'ver' => 150], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new IntegerType();
        $sut->fromArray(['val' => null, 'type' => 'INT', 'ver' => 0]);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'INT', 'val' => 150, 'ver' => 10]);
        $this->assertEquals(150, $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new IntegerType();
        try {
            $sut->fromArray(['val' => 140]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT', 'val' => 150]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT', 'val' => new \stdClass(), 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 3, 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new IntegerType(150, ['ver' => 10]);
        $sut2 = new IntegerType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals(150, $sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new IntegerType();
        $this->assertNull($sut->getValue());

        $sut->setValue(150);
        $this->assertEquals(150, $sut->getValue());

        $sut->setValue(-1);
        $this->assertEquals(-1, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new IntegerType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new IntegerType();
        $this->assertEquals(0, $sut->getVersion());
        $sut->setVersion(10);
        $this->assertEquals(10, $sut->getVersion());

        try {
            $sut->setVersion(-1);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid version: -1', $e->getMessage());
        }
    }

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new IntegerType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new IntegerType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new IntegerType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new IntegerType(250, ['ver' => 10]);
        $sut->merge(new IntegerType(150, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        // Merging same version
        $sut = new IntegerType(250, ['ver' => 10]);
        $sut->merge(new IntegerType(150, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        $sut = new IntegerType(150, ['ver' => 10]);
        $sut->merge(new IntegerType(250, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        // Merging greater version
        $sut = new IntegerType(350, ['ver' => 10]);
        $sut->merge(new IntegerType(250, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new IntegerType(250, ['ver' => 10]);
        $sut->merge(new IntegerType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        $sut = new IntegerType(null, ['ver' => 10]);
        $sut->merge(new IntegerType(250, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new IntegerType(250, ['ver' => 10]);
        $sut->merge(new IntegerType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        $sut = new IntegerType(null, ['ver' => 10]);
        $sut->merge(new IntegerType(250, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());

        $sut = new IntegerType(null, ['ver' => 10]);
        $sut->merge(new IntegerType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging greater version
        $sut = new IntegerType(350, ['ver' => 10]);
        $sut->merge(new IntegerType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new IntegerType(null, ['ver' => 10]);
        $sut->merge(new IntegerType(250, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame(250, $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new IntegerType(2);
        $obj2 = new IntegerType(1);

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new IntegerType(2);
        $obj2 = new IntegerType(1);
        $obj3 = new IntegerType(2);

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new IntegerType(250, ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with INT.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isValidValue(1000));
        $this->assertTrue($sut->isValidValue(3.14));
        $this->assertFalse($sut->isValidValue('Invalid email'));
    }

    public function testIsValidNull(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new IntegerType();
        $this->assertEquals(150, $sut->coerce(150));

        // type juggling
        $this->assertEquals(150, $sut->coerce(150.1));
        $this->assertEquals(151, $sut->coerce(150.9));
    }

    public function testCoerceNull(): void
    {
        $sut = new IntegerType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new IntegerType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new EmailType();
        $this->assertNull($sut->getSerializedValue());
        $sut->setValue('simpkevin@gmail.com');
        $this->assertEquals('simpkevin@gmail.com', $sut->getSerializedValue());
    }
}
