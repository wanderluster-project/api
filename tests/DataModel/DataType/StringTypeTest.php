<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\StringType;
use App\DataModel\Translation\LanguageCodes;
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
            $sut = new StringType();
            $sut->setValue(123, ['lang' => 'en']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to STRING data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new StringType(['en' => 123]);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to STRING data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog', 'es' => 'El rápido zorro marrón salta sobre el perro perezoso']);
        $this->assertEquals(['en', 'es'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $string1 = new StringType(['en' => 'ABC', 'es' => 'DEF', 'ja' => 'GHI'], ['ver' => 10]);
        $string2 = new StringType(['en' => 'JKL', 'es' => 'MNO', 'fr' => 'PQR'], ['ver' => 9]);
        $string1->merge($string2);
        $this->assertSame(10, $string1->getVersion());
        $this->assertSame('ABC', $string1->getValue(['lang' => 'en']));
        $this->assertSame('DEF', $string1->getValue(['lang' => 'es']));
        $this->assertSame('GHI', $string1->getValue(['lang' => 'ja']));
        $this->assertNull($string1->getValue(['lang' => 'fr']));

        // Merging same version
        $string1 = new StringType(['en' => 'ABC', 'es' => 'MNO', 'ja' => 'GHI'], ['ver' => 10]);
        $string2 = new StringType(['en' => 'JKL', 'es' => 'DEF', 'fr' => 'PQR'], ['ver' => 10]);
        $string1->merge($string2);
        $this->assertSame(10, $string1->getVersion());
        $this->assertSame('JKL', $string1->getValue(['lang' => 'en']));
        $this->assertSame('MNO', $string1->getValue(['lang' => 'es']));
        $this->assertSame('GHI', $string1->getValue(['lang' => 'ja']));
        $this->assertSame('PQR', $string1->getValue(['lang' => 'fr']));

        $string1 = new StringType(['en' => 'JKL', 'es' => 'DEF', 'fr' => 'PQR'], ['ver' => 10]);
        $string2 = new StringType(['en' => 'ABC', 'es' => 'MNO', 'ja' => 'GHI'], ['ver' => 10]);
        $string1->merge($string2);
        $this->assertSame(10, $string1->getVersion());
        $this->assertSame('JKL', $string1->getValue(['lang' => 'en']));
        $this->assertSame('MNO', $string1->getValue(['lang' => 'es']));
        $this->assertSame('GHI', $string1->getValue(['lang' => 'ja']));
        $this->assertSame('PQR', $string1->getValue(['lang' => 'fr']));

        // merging newer version
        $string1 = new StringType(['en' => 'ABC', 'es' => 'DEF', 'ja' => 'GHI'], ['ver' => 9]);
        $string2 = new StringType(['en' => 'JKL', 'es' => 'MNO', 'fr' => 'PQR'], ['ver' => 10]);
        $string1->merge($string2);
        $this->assertSame(10, $string1->getVersion());
        $this->assertSame('JKL', $string1->getValue(['lang' => 'en']));
        $this->assertSame('MNO', $string1->getValue(['lang' => 'es']));
        $this->assertNull($string1->getValue(['lang' => 'ja']));
        $this->assertSame('PQR', $string1->getValue(['lang' => 'fr']));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new StringType(['en' => 'ABC', 'es' => 'DEF', 'ja' => 'GHI'], ['ver' => 9]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with STRING.', $e->getMessage());
        }
    }
}
