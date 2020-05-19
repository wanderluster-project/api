<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType\String;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\String\UrlType;
use App\Exception\WanderlusterException;
use App\Tests\DataModel\DataType\TypeTestInterface;
use App\Tests\Fixtures\StringObject;
use PHPUnit\Framework\TestCase;

class UrlTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new UrlType();
        $this->assertEquals('URL', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new UrlType();
        $this->assertTrue($sut->isNull());

        $sut = new UrlType('https://www.google.com');
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new UrlType('https://www.google.com');
        $this->assertEquals('https://www.google.com', $sut->getValue());
    }

    public function testTranslations(): void
    {
        // url doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // url doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new UrlType();
        $this->assertEquals(['val' => null, 'type' => 'URL', 'ver' => 0], $sut->toArray());

        $sut = new UrlType('https://www.google.com');
        $this->assertEquals(['val' => 'https://www.google.com', 'type' => 'URL', 'ver' => 0], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new UrlType();
        $sut->fromArray(['val' => null, 'type' => 'URL', 'ver' => 0]);
        $this->assertNull($sut->getValue());
        $this->assertEquals(0, $sut->getVersion());

        $sut->fromArray(['type' => 'URL', 'val' => 'https://www.google.com', 'ver' => 10]);
        $this->assertEquals('https://www.google.com', $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new UrlType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating URL data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating URL data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'val' => 'https://www.google.com']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating URL data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'ver' => 10, 'val' => 3.14]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
        }

        // invalid value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'ver' => 10, 'val' => 'simpkevin']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'foo', 'ver' => 10, 'val' => 'https://www.google.com']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating URL data type - Invalid Type: foo.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new UrlType('https://www.google.com', ['ver' => 10]);
        $sut2 = new UrlType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals('https://www.google.com', $sut2->getValue());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new UrlType();
        $this->assertNull($sut->getValue());

        $sut->setValue('https://www.google.com');
        $this->assertEquals('https://www.google.com', $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new UrlType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new UrlType();
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
            $sut = new UrlType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new UrlType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new UrlType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new UrlType('https://www.google.com', ['ver' => 10]);
        $sut->merge(new UrlType('https://www.yahoo.com', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());

        // Merging same version
        $sut = new UrlType('https://www.google.com', ['ver' => 10]);
        $sut->merge(new UrlType('https://www.yahoo.com', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.yahoo.com', $sut->getValue());

        $sut = new UrlType('https://www.yahoo.com', ['ver' => 10]);
        $sut->merge(new UrlType('https://www.google.com', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.yahoo.com', $sut->getValue());

        // Merging greater version
        $sut = new UrlType('https://www.yahoo.com', ['ver' => 10]);
        $sut->merge(new UrlType('https://www.google.com', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new UrlType('https://www.google.com', ['ver' => 10]);
        $sut->merge(new UrlType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());

        $sut = new UrlType(null, ['ver' => 10]);
        $sut->merge(new UrlType('https://www.yahoo.com', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new UrlType('https://www.google.com', ['ver' => 10]);
        $sut->merge(new UrlType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());

        $sut = new UrlType(null, ['ver' => 10]);
        $sut->merge(new UrlType('https://www.google.com', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());

        // Merging greater version
        $sut = new UrlType('https://www.yahoo.com', ['ver' => 10]);
        $sut->merge(new UrlType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new UrlType(null, ['ver' => 10]);
        $sut->merge(new UrlType('https://www.google.com', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new UrlType('https://www.yahoo.com');
        $obj2 = new UrlType('https://www.google.com');

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new UrlType('https://www.yahoo.com');
        $obj2 = new UrlType('https://www.google.com');
        $obj3 = new UrlType('https://www.yahoo.com');

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new UrlType('https://www.yahoo.com', ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with URL.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new UrlType();
        $this->assertTrue($sut->isValidValue('https://www.google.com'));
        $this->assertFalse($sut->isValidValue('I am invalid'));
    }

    public function testIsValidNull(): void
    {
        $sut = new UrlType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new UrlType();
        $this->assertEquals('https://www.google.com', $sut->coerce('https://www.google.com'));
        $this->assertEquals('https://www.google.com', $sut->coerce(new StringObject('https://www.google.com')));
    }

    public function testCoerceNull(): void
    {
        $sut = new UrlType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new UrlType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
        }

        try {
            $sut = new UrlType();
            $sut->coerce(new \stdClass());
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type.', $e->getMessage());
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
