<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType\String;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\String\LocalizedStringType;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\DataModel\DataType\TypeTestInterface;
use App\Tests\Fixtures\StringObject;
use Exception;
use PHPUnit\Framework\TestCase;

class LocalizedStringTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new LocalizedStringType();
        $this->assertTrue($sut->isNull(['lang' => LanguageCodes::SPANISH]));
        $this->assertTrue($sut->isNull(['lang' => LanguageCodes::ENGLISH]));
    }

    public function testIsNull(): void
    {
        $sut = new LocalizedStringType();
        $this->assertTrue($sut->isNull(['lang' => LanguageCodes::SPANISH]));
        $this->assertTrue($sut->isNull(['lang' => LanguageCodes::ENGLISH]));
        try {
            $sut->isNull();
            $this->fail('Exception not thown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }
        try {
            $this->assertTrue($sut->isNull(['lang' => LanguageCodes::ANY]));
            $this->fail('Exception not thown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }

        $sut = new LocalizedStringType();
        $sut->setTranslation(LanguageCodes::ENGLISH, 'Dog');
        $sut->setTranslation(LanguageCodes::SPANISH, 'Perro');
        $this->assertFalse($sut->isNull(['lang' => LanguageCodes::ENGLISH]));
        $this->assertFalse($sut->isNull(['lang' => LanguageCodes::SPANISH]));
    }

    public function testConstructorWithValue(): void
    {
        // no constructor aruguments provided to localizedStringType
        $this->assertTrue(true);
    }

    public function testTranslations(): void
    {
        $sut = new LocalizedStringType();
        $sut->setTranslation(LanguageCodes::ENGLISH, 'Dog');
        $sut->setTranslation(LanguageCodes::SPANISH, 'Perro');
        $this->assertEquals('Dog', $sut->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut->getTranslation(LanguageCodes::SPANISH));
        $sut->setTranslation(LanguageCodes::ENGLISH, null);
        $sut->setTranslation(LanguageCodes::SPANISH, null);
        $this->assertNull($sut->getTranslation(LanguageCodes::ENGLISH));
        $this->assertNull($sut->getTranslation(LanguageCodes::SPANISH));
    }

    public function testTranslationsException(): void
    {
        $sut = new LocalizedStringType();
        try {
            $sut->setValue(new \stdClass(), ['lang' => LanguageCodes::ENGLISH, 'ver' => 10]);
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to LOCALIZED_STRING data type.', $e->getMessage());
        }
    }

    public function testToArray(): void
    {
        $sut = new LocalizedStringType();
        $sut->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut->setTranslation(LanguageCodes::SPANISH, 'Perro', 15);
        $this->assertSame(
            ['type' => 'LOCALIZED_STRING',
                'val' => [
                    ['type' => 'TRANS', 'val' => 'Dog', 'ver' => 10, 'lang' => 'en'],
                    ['type' => 'TRANS', 'val' => 'Perro', 'ver' => 15, 'lang' => 'es'],
                ],
            ],
            $sut->toArray()
        );
    }

    public function testFromArray(): void
    {
        $sut = new LocalizedStringType();
        $sut->fromArray(
            ['type' => 'LOCALIZED_STRING',
                'val' => [
                    ['type' => 'TRANS', 'val' => 'Dog', 'ver' => 10, 'lang' => 'en'],
                    ['type' => 'TRANS', 'val' => 'Perro', 'ver' => 15, 'lang' => 'es'],
                ],
            ],
        );
        $this->assertEquals('Dog', $sut->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut->getTranslation(LanguageCodes::SPANISH));
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new LocalizedStringType();
        $sut2 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 15);
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new LocalizedStringType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating LOCALIZED_STRING data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new LocalizedStringType();
        try {
            $sut->fromArray(['type' => 'LOCALIZED_STRING']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating LOCALIZED_STRING data type - Missing Field: val.', $e->getMessage());
        }

        // invalid value
        $sut = new LocalizedStringType();
        try {
            $sut->fromArray(['type' => 'LOCALIZED_STRING', 'val' => 'foo']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating LOCALIZED_STRING data type - field val must be an array.', $e->getMessage());
        }

        // invalid type
        $sut = new LocalizedStringType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => [], 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating LOCALIZED_STRING data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new LocalizedStringType();
        $sut->setValue('Dog', ['lang' => LanguageCodes::ENGLISH, 'ver' => 10]);
        $sut->setValue('Perro', ['lang' => LanguageCodes::SPANISH, 'ver' => 20]);
        $this->assertEquals('Dog', $sut->getValue(['lang' => LanguageCodes::ENGLISH]));
        $this->assertEquals('Perro', $sut->getValue(['lang' => LanguageCodes::SPANISH]));

        // exceptions
        try {
            $sut->setValue('Dog');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }
        try {
            $sut->setValue('Dog', ['lang' => LanguageCodes::ANY]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }
        try {
            $sut->getValue();
        } catch (WanderlusterException $e) {
            $this->assertEquals('Configuration option missing - lang.', $e->getMessage());
        }
        try {
            $sut->getValue(['lang' => LanguageCodes::ANY]);
        } catch (WanderlusterException $e) {
            $this->assertEquals('You must specify a language.  Wildcard (*) is not allowed).', $e->getMessage());
        }
    }

    public function testSetGetNull(): void
    {
        $sut = new LocalizedStringType();
        $sut->setValue(null, ['lang' => LanguageCodes::ENGLISH, 'ver' => 10]);
        $sut->setValue(null, ['lang' => LanguageCodes::SPANISH, 'ver' => 20]);
        $this->assertNull($sut->getValue(['lang' => LanguageCodes::ENGLISH]));
        $this->assertNull($sut->getValue(['lang' => LanguageCodes::SPANISH]));
    }

    public function testSetGetVersion(): void
    {
        try {
            $sut = new LocalizedStringType();
            $sut->setVersion(10);
        } catch (WanderlusterException $e) {
            $this->assertEquals('Unable to set version for data type: LOCALIZED_STRING', $e->getMessage());
        }
    }

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new LocalizedStringType();
            $sut->setValue(new \stdClass());
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to LOCALIZED_STRING data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        // not applicable to localized string type
        $this->assertTrue(true);
    }

    public function testGetLanguages(): void
    {
        $sut = new LocalizedStringType();
        $sut->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut->setTranslation(LanguageCodes::SPANISH, 'Perro', 15);
        $this->assertEquals(['en', 'es'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // merging greater version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 100);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Apple', 20);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Manzana', 120);
        $sut2->setTranslation(LanguageCodes::FRENCH, 'Chienne', 120);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Apple', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Manzana', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Apple', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Manzana', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging same version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 10);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Apple', 10);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Manzana', 10);
        $sut2->setTranslation(LanguageCodes::FRENCH, 'Chienne', 120);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Dog', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging lesser version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 10);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Apple', 5);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Manzana', 5);
        $sut2->setTranslation(LanguageCodes::FRENCH, 'Chienne', 5);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Dog', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // each language merges independently
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 1);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 10);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Apple', 5);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Manzana', 5);
        $sut2->setTranslation(LanguageCodes::FRENCH, 'Chienne', 5);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Apple', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Apple', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));
    }

    public function testMergeNull(): void
    {
        // merging greater version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 100);
        $sut1->setTranslation(LanguageCodes::FRENCH, 'Chienne', 5);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, null, 20);
        $sut2->setTranslation(LanguageCodes::SPANISH, null, 120);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals(null, $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals(null, $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging greater version, switching which one is null
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, null, 10);
        $sut2->setTranslation(LanguageCodes::SPANISH, null, 100);
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 20);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 120);
        $sut1->setTranslation(LanguageCodes::FRENCH, 'Chienne', 50);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Dog', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging lesser version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 100);
        $sut1->setTranslation(LanguageCodes::FRENCH, 'Chienne', 5);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, null, 20);
        $sut2->setTranslation(LanguageCodes::SPANISH, null, 120);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals(null, $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals(null, $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals(null, $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging lesser version, switching which one is null
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, null, 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, null, 100);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Dog', 20);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Perro', 120);
        $sut2->setTranslation(LanguageCodes::FRENCH, 'Chienne', 50);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Dog', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));

        // merging same version
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 100);
        $sut1->setTranslation(LanguageCodes::FRENCH, 'Chienne', 5);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, null, 10);
        $sut2->setTranslation(LanguageCodes::SPANISH, null, 100);
        $sut1->merge($sut2);
        $sut2->merge($sut1);
        $this->assertEquals('Dog', $sut1->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut1->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut1->getTranslation(LanguageCodes::FRENCH));
        $this->assertEquals('Dog', $sut2->getTranslation(LanguageCodes::ENGLISH));
        $this->assertEquals('Perro', $sut2->getTranslation(LanguageCodes::SPANISH));
        $this->assertEquals('Chienne', $sut2->getTranslation(LanguageCodes::FRENCH));
    }

    public function testMergeException(): void
    {
        $sut = new LocalizedStringType();
        try {
            $sut->merge(new BooleanType(true));
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Unable to merge BOOL with LOCALIZED_STRING.', $e->getMessage());
        }
    }

    public function testIsGreaterThan(): void
    {
        $sut = new LocalizedStringType();
        try {
            $sut->isGreaterThan(new LocalizedStringType());
            $this->fail('Exception not thrown.');
        } catch (Exception $e) {
            $this->assertEquals('Unable to use comparisons with data type: LOCALIZED_STRING.', $e->getMessage());
        }
    }

    public function testIsEqualTo(): void
    {
        $sut1 = new LocalizedStringType();
        $sut1->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut1->setTranslation(LanguageCodes::SPANISH, 'Perro', 10);
        $sut2 = new LocalizedStringType();
        $sut2->setTranslation(LanguageCodes::ENGLISH, 'Apple', 10);
        $sut2->setTranslation(LanguageCodes::SPANISH, 'Manzana', 10);
        try {
            $sut1->isEqualTo($sut2);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Unable to use comparisons with data type: LOCALIZED_STRING.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new LocalizedStringType();
        $this->assertTrue($sut->isValidValue('foo'));
        $this->assertFalse($sut->isValidValue(new \stdClass()));
    }

    public function testIsValidNull(): void
    {
        $sut = new LocalizedStringType();
        $this->assertTrue($sut->isValidValue(true));
    }

    public function testCoerce(): void
    {
        $sut = new LocalizedStringType();
        $this->assertEquals('simpkevin@gmail.com', $sut->coerce('simpkevin@gmail.com'));
        $this->assertEquals('simpkevin@gmail.com', $sut->coerce(new StringObject('simpkevin@gmail.com')));
    }

    public function testCoerceNull(): void
    {
        $sut = new LocalizedStringType();
        $this->assertEquals(null, $sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        $sut = new LocalizedStringType();
        try {
            $this->assertEquals(null, $sut->coerce(new \stdClass()));
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to LOCALIZED_STRING data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new LocalizedStringType();
        $sut->setTranslation(LanguageCodes::ENGLISH, 'Dog', 10);
        $sut->setTranslation(LanguageCodes::SPANISH, 'Perro', 10);
        $this->assertEquals([
            [
                'type' => 'TRANS',
                'val' => 'Dog',
                'ver' => 10,
                'lang' => 'en',
            ],
            [
                'type' => 'TRANS',
                'val' => 'Perro',
                'ver' => 10,
                'lang' => 'es',
            ],
        ], $sut->getSerializedValue());
    }
}
