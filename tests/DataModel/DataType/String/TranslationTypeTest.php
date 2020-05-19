<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType\String;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\String\TranslationType;
use App\DataModel\Translation\LanguageCodes;
use App\Exception\WanderlusterException;
use App\Tests\DataModel\DataType\TypeTestInterface;
use App\Tests\Fixtures\StringObject;
use PHPUnit\Framework\TestCase;

class TranslationTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new TranslationType();
        $this->assertEquals('TRANS', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
        $this->assertNull($sut->getValue());
        $this->assertEquals(null, $sut->getLanguage());
    }

    public function testIsNull(): void
    {
        $sut = new TranslationType();
        $this->assertTrue($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new TranslationType('Dog', ['lang' => LanguageCodes::ENGLISH]);
        $this->assertFalse($sut->isNull());
    }

    public function testTranslations(): void
    {
        $sut = new TranslationType('Dog', ['lang' => LanguageCodes::ENGLISH]);
        $this->assertEquals([LanguageCodes::ENGLISH], $sut->getLanguages());
    }

    public function testGetTranslation(): void
    {
        $sut = new TranslationType('Dog', ['lang' => LanguageCodes::ENGLISH]);
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLanguage());
    }

    public function testTranslationsException(): void
    {
        // no exceptions thrown
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new TranslationType('Dog', ['lang' => LanguageCodes::ENGLISH]);
        $this->assertEquals(['type' => 'TRANS', 'val' => 'Dog', 'ver' => 0, 'lang' => LanguageCodes::ENGLISH], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new TranslationType();
        $sut->fromArray(['type' => 'TRANS', 'val' => 'Perro', 'ver' => 10, 'lang' => LanguageCodes::SPANISH]);
        $this->assertEquals(10, $sut->getVersion());
        $this->assertEquals('Perro', $sut->getValue());
        $this->assertEquals(LanguageCodes::SPANISH, $sut->getLanguage());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new TranslationType();
        try {
            $sut->fromArray(['val' => 'Perro', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating TRANS data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new TranslationType();
        try {
            $sut->fromArray(['type' => 'TRANS', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating TRANS data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new TranslationType();
        try {
            $sut->fromArray(['type' => 'TRANS', 'val' => 'Perro']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating TRANS data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new TranslationType();
        try {
            $sut->fromArray(['type' => 'TRANS', 'val' => new \stdClass(), 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to TRANS data type.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new TranslationType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 'Perro', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating TRANS data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testCompositionToFromArray(): void
    {
        $sut1 = new TranslationType('Dog', ['lang' => LanguageCodes::ENGLISH, 'ver' => 10]);
        $sut2 = new TranslationType();
        $sut2->fromArray($sut1->toArray());
        $this->assertEquals('Dog', $sut2->getValue());
        $this->assertEquals(LanguageCodes::ENGLISH, $sut2->getLanguage());
        $this->assertEquals(10, $sut2->getVersion());
    }

    public function testSetGet(): void
    {
        $sut = new TranslationType();
        $this->assertNull($sut->getValue());

        $sut->setValue('Dog', ['lang' => LanguageCodes::ENGLISH]);
        $this->assertEquals('Dog', $sut->getValue());
        $this->assertEquals(LanguageCodes::ENGLISH, $sut->getLanguage());
    }

    public function testSetGetNull(): void
    {
        $sut = new TranslationType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new TranslationType();
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
            $sut = new TranslationType();
            $sut->setValue(new \stdClass());
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to TRANS data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new TranslationType(new \stdClass());
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to TRANS data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new TranslationType();
        $this->assertEquals([], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new TranslationType('version 10', ['ver' => 10]);
        $sut->merge(new TranslationType('version 9', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10', $sut->getValue());

        // Merging same version
        $sut = new TranslationType('version 10a', ['ver' => 10]);
        $sut->merge(new TranslationType('version 10b', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10b', $sut->getValue());

        $sut = new TranslationType('version 10b', ['ver' => 10]);
        $sut->merge(new TranslationType('version 10a', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10b', $sut->getValue());

        // Merging greater version
        $sut = new TranslationType('version 10', ['ver' => 10]);
        $sut->merge(new TranslationType('version 11', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('version 11', $sut->getValue());
    }

    public function testMergeNull(): void
    {
        // Merging previous version
        $sut = new TranslationType('version 10', ['ver' => 10]);
        $sut->merge(new TranslationType(null, ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10', $sut->getValue());

        $sut = new TranslationType(null, ['ver' => 10]);
        $sut->merge(new TranslationType('version 9', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertNull($sut->getValue());

        // Merging same version
        $sut = new TranslationType('version 10a', ['ver' => 10]);
        $sut->merge(new TranslationType(null, ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10a', $sut->getValue());

        $sut = new TranslationType(null, ['ver' => 10]);
        $sut->merge(new TranslationType('version 10a', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('version 10a', $sut->getValue());

        // Merging greater version
        $sut = new TranslationType('version 10', ['ver' => 10]);
        $sut->merge(new TranslationType(null, ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertNull($sut->getValue());

        $sut = new TranslationType(null, ['ver' => 10]);
        $sut->merge(new TranslationType('version 11', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('version 11', $sut->getValue());
    }

    public function testIsGreaterThan(): void
    {
        $obj1 = new TranslationType('xyz');
        $obj2 = new TranslationType('abc');

        $this->assertTrue($obj1->isGreaterThan($obj2));
        $this->assertFalse($obj2->isGreaterThan($obj1));
    }

    public function testIsEqualTo(): void
    {
        $obj1 = new TranslationType('xyz');
        $obj2 = new TranslationType('abc');
        $obj3 = new TranslationType('xyz');

        $this->assertFalse($obj1->isEqualTo($obj2));
        $this->assertTrue($obj1->isEqualTo($obj3));
    }

    public function testMergeException(): void
    {
        try {
            $sut = new TranslationType('version 10', ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with TRANS.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new TranslationType();
        $this->assertTrue($sut->isValidValue('test 1..2..3'));
        $this->assertFalse($sut->isValidValue(new \stdClass()));
    }

    public function testIsValidNull(): void
    {
        $sut = new TranslationType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new TranslationType();
        $this->assertEquals('3.14', $sut->coerce(3.14));
        $this->assertEquals('foo', $sut->coerce(new StringObject('foo')));
        $this->assertEquals('TRUE', $sut->coerce(true));
        $this->assertEquals('FALSE', $sut->coerce(false));
    }

    public function testCoerceNull(): void
    {
        $sut = new TranslationType();
        $this->assertNull($sut->coerce(null));
    }

    public function testCoerceException(): void
    {
        $sut = new TranslationType();
        try {
            $sut->coerce(new \stdClass());
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to TRANS data type.', $e->getMessage());
        }
    }

    public function testGetSerializedValue(): void
    {
        $sut = new TranslationType();
        $this->assertNull($sut->getSerializedValue());

        $sut->setValue('Dog');
        $this->assertEquals('Dog', $sut->getSerializedValue());

        // correctly coerces numbers to strings when serializing
        $sut->setValue(3.14);
        $this->assertEquals('3.14', $sut->getSerializedValue());
    }
}
