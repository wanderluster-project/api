<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Translation\LanguageCodes;
use App\DataModel\Types\StringType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class StringTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new StringType();
        $this->assertTrue($sut->isNull(['lang' => 'en']));
    }

    public function testIsNull(): void
    {
        $sut = new StringType();
        $this->assertTrue($sut->isNull(['lang' => 'en']));

        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog']);
        $this->assertFalse($sut->isNull(['lang' => 'en']));

        // exceptions
        try {
            $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog']);
            $sut->isNull(['lang' => LanguageCodes::ANY]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }
    }

    public function testConstructorWithValue(): void
    {
        $sut = new StringType(['en' => 'foo bar']);
        $this->assertEquals('foo bar', $sut->getValue(['lang' => 'en']));
    }

    public function testTranslations(): void
    {
        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog', 'es' => 'El rápido zorro marrón salta sobre el perro perezoso']);
        $this->assertEquals('The quick brown fox jumps over the lazy dog', $sut->getValue(['lang' => 'en']));
        $this->assertEquals('El rápido zorro marrón salta sobre el perro perezoso', $sut->getValue(['lang' => 'es']));
    }

    public function testTranslationsException(): void
    {
        // setting without a lang parameter
        try {
            $sut = new StringType();
            $sut->setValue('The quick brown fox jumps over the lazy dog');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }

        try {
            $sut = new StringType();
            $sut->setValue('The quick brown fox jumps over the lazy dog', ['lang' => '']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }

        // getting without a lang parameter
        try {
            $sut = new StringType();
            $sut->setValue('The quick brown fox jumps over the lazy dog', ['lang' => 'en']);
            $sut->getValue();
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }
    }

    public function testToArray(): void
    {
        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog', 'es' => 'El rápido zorro marrón salta sobre el perro perezoso'], ['ver' => 10]);
        $this->assertEquals(
            [
                'type' => 'STRING',
                'val' => [
                    'en' => 'The quick brown fox jumps over the lazy dog',
                    'es' => 'El rápido zorro marrón salta sobre el perro perezoso',
                ],
                'ver' => 10,
            ],
            $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new StringType();
        $this->assertTrue($sut->isNull(['lang' => 'en']));
        $sut->fromArray([
            'type' => 'STRING',
            'val' => [
                'en' => 'The quick brown fox jumps over the lazy dog',
                'es' => 'El rápido zorro marrón salta sobre el perro perezoso',
            ],
            'ver' => 10,
        ]);
        $this->assertEquals('The quick brown fox jumps over the lazy dog', $sut->getValue(['lang' => 'en']));
        $this->assertEquals('El rápido zorro marrón salta sobre el perro perezoso', $sut->getValue(['lang' => 'es']));
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new StringType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'STRING']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'STRING', 'val' => []]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'STRING', 'val' => 'I AM INVALID', 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - val should be an array.', $e->getMessage());
        }

        // invalid value
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => ['en' => 'The quick brown fox jumps over the lazy dog'], 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new StringType();
        $this->assertNull($sut->getValue(['lang' => 'en']));

        $sut->setValue('foo', ['lang' => 'en']);
        $this->assertEquals('foo', $sut->getValue(['lang' => 'en']));

        $sut->setValue('bar', ['lang' => 'es']);
        $this->assertEquals('bar', $sut->getValue(['lang' => 'es']));

        // Exceptions
        try {
            $sut->setValue('bar', ['lang' => LanguageCodes::ANY]);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        try {
            $sut->getValue(['lang' => LanguageCodes::ANY]);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }
    }

    public function testSetGetNull(): void
    {
        $sut = new StringType();
        $this->assertNull($sut->getValue(['lang' => 'es']));
        $sut->setValue(null, ['lang' => 'es']);
        $this->assertNull($sut->getValue(['lang' => 'es']));
    }

    public function testSetGetVersion(): void
    {
        $sut = new StringType();
        $this->assertEquals(0, $sut->getVersion());
        $sut->setVersion(10);
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new StringType();
            $sut->setValue(123, ['lang' => 'en']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to STRING data type - String required.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new StringType(['en' => 123]);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to STRING data type - String required.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog', 'es' => 'El rápido zorro marrón salta sobre el perro perezoso']);
        $this->assertEquals(['en', 'es'], $sut->getLanguages());
    }
}
