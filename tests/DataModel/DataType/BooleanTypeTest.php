<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\DateTimeType;
use App\DataModel\DataType\String\EmailType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class BooleanTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new BooleanType();
        $this->assertEquals('BOOL', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new BooleanType();
        $this->assertTrue($sut->isNull());

        $sut = new BooleanType(true);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new BooleanType(true);
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(false);
        $this->assertFalse($sut->getValue());
    }

    public function testTranslations(): void
    {
        // boolean doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // boolean doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new BooleanType();
        $this->assertEquals(['val' => null, 'type' => 'BOOL', 'ver' => 0], $sut->toArray());

        $sut = new BooleanType(true, ['ver' => 10]);
        $this->assertEquals(['val' => true, 'type' => 'BOOL', 'ver' => 10], $sut->toArray());

        $sut = new BooleanType(false, ['ver' => 50]);
        $this->assertEquals(['val' => false, 'type' => 'BOOL', 'ver' => 50], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new BooleanType();
        $sut->fromArray(['val' => null, 'type' => 'BOOL', 'ver' => 0]);
        $this->assertNull($sut->getValue());
        $this->assertEquals(0, $sut->getVersion());

        $sut->fromArray(['type' => 'BOOL', 'val' => true, 'ver' => 10]);
        $this->assertTrue($sut->getValue());
        $this->assertEquals(10, $sut->getVersion());

        $sut->fromArray(['type' => 'BOOL', 'val' => false, 'ver' => 100]);
        $this->assertFalse($sut->getValue());
        $this->assertEquals(100, $sut->getVersion());
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new BooleanType(true, ['ver' => 10]);
        $sut2 = new BooleanType();
        $sut2->fromArray($sut1->toArray());
        $this->assertTrue($sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new BooleanType();
        try {
            $sut->fromArray(['val' => true]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating BOOL data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new BooleanType();
        try {
            $sut->fromArray(['type' => 'BOOL']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating BOOL data type - Missing Field: val.', $e->getMessage());
        }

        // invalid value
        $sut = new BooleanType();
        try {
            $sut->fromArray(['type' => 'BOOL', 'val' => new \stdClass(), 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to BOOL data type.', $e->getMessage());
        }

        // invalid type
        $sut = new BooleanType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => true, 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating BOOL data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->getValue());

        $sut->setValue(true);
        $this->assertTrue($sut->getValue());

        $sut->setValue(false);
        $this->assertFalse($sut->getValue());

        $sut->setValue(null);
        $this->assertNull($sut->getValue());

        // exceptions
        try{
            $sut->setValue('woot');
            $this->fail('Exception not thrown.');
        }catch(WanderlusterException $e){
            $this->assertEquals('Invalid value passed to BOOL data type.', $e->getMessage());
        }
    }

    public function testSetGetNull(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new BooleanType();
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
            $sut = new BooleanType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to BOOL data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new BooleanType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to BOOL data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new BooleanType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new BooleanType(true, ['ver' => 10]);
        $sut->merge(new BooleanType(false, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        // Merging same version
        $sut = new BooleanType(true, ['ver' => 10]);
        $sut->merge(new BooleanType(false, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(false, ['ver' => 10]);
        $sut->merge(new BooleanType(true, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        // Merging greater version
        $sut = new BooleanType(false, ['ver' => 10]);
        $sut->merge(new BooleanType(true, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertTrue($sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new BooleanType(true, ['ver' => 10]);
        $sut->merge(new BooleanType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(null, ['ver' => 10]);
        $sut->merge(new BooleanType(true, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new BooleanType(true, ['ver' => 10]);
        $sut->merge(new BooleanType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(null, ['ver' => 10]);
        $sut->merge(new BooleanType(true, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(null, ['ver' => 10]);
        $sut->merge(new BooleanType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging greater version
        $sut = new BooleanType(false, ['ver' => 10]);
        $sut->merge(new BooleanType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new BooleanType(null, ['ver' => 10]);
        $sut->merge(new BooleanType(false, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertFalse($sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new BooleanType(true);
        $obj2 = new BooleanType(false);

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));

        try{
            $obj1->isGreaterThan(new EmailType());
            $this->fail('Exception not thrown.');
        }catch(WanderlusterException $e){
            $this->assertEquals('Unable to compare EMAIL with BOOL.',$e->getMessage());
        }
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new BooleanType(true);
        $obj2 = new BooleanType(false);
        $obj3 = new BooleanType(true);

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));

        try{
            $obj1->isEqualTo(new EmailType());
            $this->fail('Exception not thrown.');
        }catch(WanderlusterException $e){
            $this->assertEquals('Unable to compare EMAIL with BOOL.',$e->getMessage());
        }
    }

    public function testMergeException(): void
    {
        $sut = new BooleanType(true, ['ver' => 10]);
        try {
            $sut->merge(new DateTimeType('1/1/20', ['ver' => 9]));
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Unable to merge DATE_TIME with BOOL.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new BooleanType();
        $this->assertTrue($sut->isValidValue(true));
        $this->assertTrue($sut->isValidValue(false));
        $this->assertFalse($sut->isValidValue('I am invalid'));
    }

    public function testIsValidNull(): void
    {
        $sut = new BooleanType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        // intrinsict data type
        $sut = new BooleanType();
        $this->assertTrue($sut->coerce(true));
        $this->assertFalse($sut->coerce(false));

        // type juggling
        $this->assertTrue($sut->coerce(1));
        $this->assertTrue($sut->coerce('T'));
        $this->assertTrue($sut->coerce('TRUE'));
        $this->assertFalse($sut->coerce(0));
        $this->assertFalse($sut->coerce('F'));
        $this->assertFalse($sut->coerce('FALSE'));
    }

    public function testCoerceNull(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new BooleanType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to BOOL data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->getSerializedValue());
        $sut->setValue(true);
        $this->assertTrue($sut->getSerializedValue());
        $sut->setValue(false);
        $this->assertFalse($sut->getSerializedValue());
    }
}
