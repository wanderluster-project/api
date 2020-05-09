<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\BooleanType;
use App\DataModel\Types\UrlType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class UrlTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new UrlType();
        $this->assertEquals('URL', $sut->getTypeId());
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
            $this->assertEquals('Invalid value passed to URL data type - String required.', $e->getMessage());
        }

        // invalid value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'ver' => 10, 'val' => 'simpkevin']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type - Invalid URL.', $e->getMessage());
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
            $this->assertEquals('Invalid version: -1', $e->getMessage());
        }
    }

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new UrlType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type - Invalid URL.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new UrlType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type - Invalid URL.', $e->getMessage());
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

        // Merging greater value
        $sut = new UrlType('https://www.yahoo.com', ['ver' => 10]);
        $sut->merge(new UrlType('https://www.google.com', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('https://www.google.com', $sut->getValue());
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
}
