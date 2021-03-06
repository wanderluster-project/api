<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\FileSizeType;
use App\DataModel\DataType\String\EmailType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class FileSizeTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new FileSizeType();
        $this->assertEquals('FILE_SIZE', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new FileSizeType();
        $this->assertTrue($sut->isNull());

        $sut = new FileSizeType('1.1 kb');
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new FileSizeType(2000);
        $this->assertEquals(2000, $sut->getValue());
    }

    public function testTranslations(): void
    {
        // file size doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // file size doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new FileSizeType();
        $this->assertEquals(['val' => null, 'type' => 'FILE_SIZE', 'ver' => 0], $sut->toArray());

        $sut = new FileSizeType('1.1GB', ['ver' => 10]);
        $this->assertEquals(['val' => floor(1.1 * FileSizeType::GB_BYTES), 'type' => 'FILE_SIZE', 'ver' => 10], $sut->toArray());

        $sut = new FileSizeType(2000, ['ver' => 10]);
        $this->assertEquals(['val' => 2000, 'type' => 'FILE_SIZE', 'ver' => 10], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new FileSizeType();
        $sut->fromArray(['val' => null, 'type' => 'FILE_SIZE', 'ver' => 10]);
        $this->assertNull($sut->getValue());
        $this->assertEquals(10, $sut->getVersion());

        $sut->fromArray(['type' => 'FILE_SIZE', 'val' => 2000, 'ver' => 10]);
        $this->assertEquals(2000, $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new FileSizeType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating FILE_SIZE data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new FileSizeType();
        try {
            $sut->fromArray(['type' => 'FILE_SIZE']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating FILE_SIZE data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new FileSizeType();
        try {
            $sut->fromArray(['type' => 'FILE_SIZE', 'val' => 2000]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating FILE_SIZE data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid type
        $sut = new FileSizeType();
        try {
            $sut->fromArray(['type' => 'TEST', 'val' => 2000, 'ver' => 150]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating FILE_SIZE data type - Invalid Type: TEST.', $e->getMessage());
        }

        // invalid value
        $sut = new FileSizeType();
        try {
            $sut->fromArray(['type' => 'FILE_SIZE', 'val' => 'I AM INVALID', 'ver' => 150]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to FILE_SIZE data type.', $e->getMessage());
        }

        // invalid value
        $sut = new FileSizeType();
        try {
            $sut->fromArray(['type' => 'FILE_SIZE', 'val' => false, 'ver' => 150]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to FILE_SIZE data type.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new FileSizeType(1000, ['ver' => 10]);
        $sut2 = new FileSizeType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals(1000, $sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new FileSizeType();
        $this->assertNull($sut->getValue());

        // GB
        $sut->setValue('1.1 GB');
        $this->assertEquals(1181116006, $sut->getValue());
        $this->assertEquals('1.10 GB', $sut->getValue(['formatted' => true]));
        $sut->setValue(1073741824);
        $this->assertEquals('1.00 GB', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('0.7 gb');
        $this->assertEquals(floor(0.7 * FileSizeType::GB_BYTES), $sut->getValue());

        // MB
        $sut->setValue('1mb');
        $this->assertEquals('1.00 MB', $sut->getValue(['formatted' => true]));
        $sut->setValue(1048576);
        $this->assertEquals('1.00 MB', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('1.1 mb');
        $this->assertEquals(floor(1.1 * FileSizeType::MB_BYTES), $sut->getValue());

        // KB
        $sut->setValue('1kb');
        $this->assertEquals('1.00 KB', $sut->getValue(['formatted' => true]));
        $sut->setValue(1024);
        $this->assertEquals('1.00 KB', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('2.5 kb');
        $this->assertEquals(floor(2.5 * FileSizeType::KB_BYTES), $sut->getValue());

        // BYTES
        $sut->setValue('10 bytes');
        $this->assertEquals('10 bytes', $sut->getValue(['formatted' => true]));
        $sut->setValue(10);
        $this->assertEquals('10 bytes', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('10 BYTES');
        $this->assertEquals(10, $sut->getValue());

        // 1 BYTE
        $sut->setValue('1 byte');
        $this->assertEquals('1 byte', $sut->getValue(['formatted' => true]));
        $sut->setValue(1);
        $this->assertEquals('1 byte', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('1 BYTE');
        $this->assertEquals(1, $sut->getValue());

        // 0 BYTES
        $sut->setValue('0 bytes');
        $this->assertEquals('0 bytes', $sut->getValue(['formatted' => true]));
        $sut->setValue(0);
        $this->assertEquals('0 bytes', $sut->getValue(['formatted' => true]));
        $sut = new FileSizeType('0 BYTES');
        $this->assertEquals(0, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new FileSizeType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new FileSizeType();
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
            $sut = new FileSizeType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to FILE_SIZE data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new FileSizeType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to FILE_SIZE data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new FileSizeType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new FileSizeType(2000, ['ver' => 10]);
        $sut->merge(new FileSizeType(1000, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(2000, $sut->getValue());

        // Merging same version
        $sut = new FileSizeType(2000, ['ver' => 10]);
        $sut->merge(new FileSizeType(1000, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(2000, $sut->getValue());

        $sut = new FileSizeType(1000, ['ver' => 10]);
        $sut->merge(new FileSizeType(2000, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(2000, $sut->getValue());

        // Merging greater version
        $sut = new FileSizeType(3000, ['ver' => 10]);
        $sut->merge(new FileSizeType(2000, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame(2000, $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new FileSizeType(2000, ['ver' => 10]);
        $sut->merge(new FileSizeType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(2000, $sut->getValue());

        $sut = new FileSizeType(null, ['ver' => 10]);
        $sut->merge(new FileSizeType(2000, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new FileSizeType(null, ['ver' => 10]);
        $sut->merge(new FileSizeType(1000, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(1000, $sut->getValue());

        $sut = new FileSizeType(1000, ['ver' => 10]);
        $sut->merge(new FileSizeType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame(1000, $sut->getValue());

        $sut = new FileSizeType(null, ['ver' => 10]);
        $sut->merge(new FileSizeType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging greater version
        $sut = new FileSizeType(3000, ['ver' => 10]);
        $sut->merge(new FileSizeType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new FileSizeType(null, ['ver' => 10]);
        $sut->merge(new FileSizeType(3000, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertEquals(3000, $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        // numeric constructor
        $obj1 = new FileSizeType(3000);
        $obj2 = new FileSizeType(2000);

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));

        // string constructor
        $obj1 = new FileSizeType('3.0 GB');
        $obj2 = new FileSizeType('2.0 GB');

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        // numeric constructor
        $obj1 = new FileSizeType(3000);
        $obj2 = new FileSizeType(2000);
        $obj3 = new FileSizeType(3000);

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));

        // string constructor
        $obj1 = new FileSizeType('3.0 GB');
        $obj2 = new FileSizeType('2.0 GB');
        $obj3 = new FileSizeType('3.0 GB');

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new FileSizeType(2000, ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with FILE_SIZE.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new FileSizeType();
        $this->assertTrue($sut->isValidValue(1000));
        $this->assertTrue($sut->isValidValue('1.5 KB'));
        $this->assertFalse($sut->isValidValue(3.14));
        $this->assertFalse($sut->isValidValue('Invalid email'));
    }

    public function testIsValidNull(): void
    {
        $sut = new FileSizeType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new FileSizeType();
        $this->assertEquals(1181116006, $sut->coerce('1.1 GB'));
    }

    public function testCoerceNull(): void
    {
        $sut = new FileSizeType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new FileSizeType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to FILE_SIZE data type.', $e->getMessage());
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
