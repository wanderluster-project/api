<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\MimeType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new MimeType();
        $this->assertEquals('MIME_TYPE', $sut->getTypeId());
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
        $this->assertEquals(['val' => null, 'type' => 'MIME_TYPE'], $sut->toArray());

        // negative value
        $sut = new MimeType('image/png');
        $this->assertEquals(['val' => 'image/png', 'type' => 'MIME_TYPE'], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new MimeType();
        $sut->fromArray(['val' => null, 'type' => 'MIME_TYPE']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 'image/png']);
        $this->assertEquals('image/png', $sut->getValue());
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

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 'test invalid']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type - Invalid MimeType.', $e->getMessage());
        }

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'MIME_TYPE', 'val' => 3.14]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type - String required.', $e->getMessage());
        }

        // invalid value
        $sut = new MimeType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 'image/png']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating MIME_TYPE data type - Invalid Type: FOO.', $e->getMessage());
        }
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

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new MimeType();
            $sut->setValue('test invalid');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type - Invalid MimeType.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new MimeType('test invalid');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to MIME_TYPE data type - Invalid MimeType.', $e->getMessage());
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
}
