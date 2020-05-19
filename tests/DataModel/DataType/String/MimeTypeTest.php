<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType\String;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\String\MimeType;
use App\Exception\WanderlusterException;
use App\Tests\DataModel\DataType\TypeTestInterface;
use App\Tests\Fixtures\StringObject;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new MimeType();
        $this->assertEquals('MIME_TYPE', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new MimeType();
        $this->assertTrue($sut->isNull());

        $sut = new MimeType('image/png');
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new MimeType('image/png');
        $this->assertEquals('image/png', $sut->getValue());
    }

    public function testTranslations(): void
    {
        // mime type doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // mime type doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        // null
        $sut = new MimeType();
        $this->assertEquals(['val' => null, 'type' => 'MIME_TYPE', 'ver' => 0], $sut->toArray());

        // negative value
        $sut = new MimeType('image/png', ['ver' => 10]);
        $this->assertEquals(['val' => 'image/png', 'type' => 'MIME_TYPE', 'ver' => 10], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new MimeType();
        $sut->fromArray(['val' => null, 'type' => 'MIME_TYPE', 'ver' => 0]);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 'image/png', 'ver' => 10]);
        $this->assertEquals('image/png', $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new MimeType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating MIME_TYPE data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating MIME_TYPE data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 'image/png']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating MIME_TYPE data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 'test invalid', 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 3.14, 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 'image/png', 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating MIME_TYPE data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new MimeType('image/png', ['ver' => 10]);
        $sut2 = new MimeType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals('image/png', $sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new MimeType();
        $this->assertNull($sut->getValue());

        $sut->setValue('image/png');
        $this->assertEquals('image/png', $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new MimeType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new MimeType();
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
            $sut = new MimeType();
            $sut->setValue('test invalid');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new MimeType('test invalid');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new MimeType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMimeTypeList(): void
    {
        $sut = new MimeType();
        $lines = file('/var/www/wanderluster/tests/Fixtures/mime-types.txt');
        $lineCount = 0;
        foreach ($lines as $row) {
            $sut->setValue(trim($row));
            ++$lineCount;
        }
        $this->assertGreaterThan(0, $lineCount);
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new MimeType('image/jpeg', ['ver' => 10]);
        $sut->merge(new MimeType('image/png', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/jpeg', $sut->getValue());

        // Merging same version
        $sut = new MimeType('image/jpeg', ['ver' => 10]);
        $sut->merge(new MimeType('image/png', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/png', $sut->getValue());

        $sut = new MimeType('image/png', ['ver' => 10]);
        $sut->merge(new MimeType('image/jpg', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/png', $sut->getValue());

        // Merging greater version
        $sut = new MimeType('image/png', ['ver' => 10]);
        $sut->merge(new MimeType('image/jpeg', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('image/jpeg', $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new MimeType('image/jpeg', ['ver' => 10]);
        $sut->merge(new MimeType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/jpeg', $sut->getValue());

        $sut = new MimeType(null, ['ver' => 10]);
        $sut->merge(new MimeType('image/png', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new MimeType(null, ['ver' => 10]);
        $sut->merge(new MimeType('image/png', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/png', $sut->getValue());

        $sut = new MimeType('image/png', ['ver' => 10]);
        $sut->merge(new MimeType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('image/png', $sut->getValue());

        // Merging greater version
        $sut = new MimeType('image/png', ['ver' => 10]);
        $sut->merge(new MimeType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new MimeType(null, ['ver' => 10]);
        $sut->merge(new MimeType('image/jpeg', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('image/jpeg', $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new MimeType('img/png');
        $obj2 = new MimeType('img/jpeg');

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new MimeType('img/png');
        $obj2 = new MimeType('img/jpeg');
        $obj3 = new MimeType('img/png');

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new MimeType('image/png', ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with MIME_TYPE.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new MimeType();
        $this->assertTrue($sut->isValidValue('img/png'));
        $this->assertFalse($sut->isValidValue(3.14));
        $this->assertFalse($sut->isValidValue('Invalid email'));
    }

    public function testIsValidNull(): void
    {
        $sut = new MimeType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new MimeType();
        $this->assertEquals('img/png', $sut->coerce('img/png'));
        $this->assertEquals('img/png', $sut->coerce(new StringObject('img/png')));
    }

    public function testCoerceNull(): void
    {
        $sut = new MimeType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new MimeType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }

        try {
            $sut = new MimeType();
            $sut->coerce(new \stdClass());
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new MimeType();
        $this->assertNull($sut->getSerializedValue());
        $sut->setValue('img/png');
        $this->assertEquals('img/png', $sut->getSerializedValue());
    }
}
