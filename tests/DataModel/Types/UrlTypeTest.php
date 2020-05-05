<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

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
        $this->assertEquals(['val' => null, 'type' => 'URL'], $sut->toArray());

        $sut = new UrlType('https://www.google.com');
        $this->assertEquals(['val' => 'https://www.google.com', 'type' => 'URL'], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new UrlType();
        $sut->fromArray(['val' => null, 'type' => 'URL']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'URL', 'val' => 'https://www.google.com']);
        $this->assertEquals('https://www.google.com', $sut->getValue());
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

        // invalid value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'val' => 3.14]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type - String required.', $e->getMessage());
        }

        // invalid value
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'URL', 'val' => 'simpkevin']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to URL data type - Invalid URL.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new UrlType();
        try {
            $sut->fromArray(['type' => 'foo', 'val' => 'https://www.google.com']);
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
}
