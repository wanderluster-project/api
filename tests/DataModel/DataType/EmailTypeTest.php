<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

use App\DataModel\DataType\BooleanType;
use App\DataModel\DataType\EmailType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class EmailTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new EmailType();
        $this->assertEquals('EMAIL', $sut->getSerializationId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new EmailType();
        $this->assertTrue($sut->isNull());

        $sut = new EmailType('simpkevin@gmail.com');
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new EmailType('simpkevin@gmail.com');
        $this->assertEquals('simpkevin@gmail.com', $sut->getValue());
    }

    public function testTranslations(): void
    {
        // email doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // email doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new EmailType();
        $this->assertEquals(['val' => null, 'type' => 'EMAIL', 'ver' => 0], $sut->toArray());

        $sut = new EmailType('simpkevin@gmail.com', ['ver' => 10]);
        $this->assertEquals(['val' => 'simpkevin@gmail.com', 'type' => 'EMAIL', 'ver' => 10], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new EmailType();
        $sut->fromArray(['val' => null, 'type' => 'EMAIL', 'ver' => 0]);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'EMAIL', 'val' => 'simpkevin@gmail.com', 'ver' => 10]);
        $this->assertEquals('simpkevin@gmail.com', $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new EmailType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating EMAIL data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'EMAIL']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating EMAIL data type - Missing Field: val.', $e->getMessage());
        }

        // invalid value
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'EMAIL', 'val' => 3.14, 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type.', $e->getMessage());
        }

        // invalid value
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'EMAIL', 'val' => 'simpkevin', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type.', $e->getMessage());
        }

        // invalid type
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 'simpkevin@gmail.com', 'ver' => 0]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating EMAIL data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new EmailType();
        $this->assertNull($sut->getValue());

        $sut->setValue('simpkevin@gmail.com');
        $this->assertEquals('simpkevin@gmail.com', $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new EmailType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new EmailType();
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
            $sut = new EmailType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new EmailType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new EmailType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }

    public function testMerge(): void
    {
        // Merging previous version
        $sut = new EmailType('simpkevin+10@gmail.com', ['ver' => 10]);
        $sut->merge(new EmailType('simpkevin+9@gmail.com', ['ver' => 9]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('simpkevin+10@gmail.com', $sut->getValue());

        // Merging same version
        $sut = new EmailType('abc0@gmail.com', ['ver' => 10]);
        $sut->merge(new EmailType('xyz@gmail.com', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('xyz@gmail.com', $sut->getValue());

        $sut = new EmailType('xyz@gmail.com', ['ver' => 10]);
        $sut->merge(new EmailType('abc0@gmail.com', ['ver' => 10]));
        $this->assertSame(10, $sut->getVersion());
        $this->assertSame('xyz@gmail.com', $sut->getValue());

        // Merging greater value
        $sut = new EmailType('xyz@gmail.com', ['ver' => 10]);
        $sut->merge(new EmailType('abc@gmail.com', ['ver' => 11]));
        $this->assertSame(11, $sut->getVersion());
        $this->assertSame('abc@gmail.com', $sut->getValue());
    }

    public function testMergeException(): void
    {
        try {
            $sut = new EmailType('simpkevin+10@gmail.com', ['ver' => 10]);
            $sut->merge(new BooleanType(true, ['ver' => 9]));
        } catch (WanderlusterException $e) {
            $this->assertSame('Unable to merge BOOL with EMAIL.', $e->getMessage());
        }
    }

    public function testIsValid(): void
    {
        $sut = new EmailType();
        $this->assertTrue($sut->isValidValue('simpkevin@gmail.com'));
        $this->assertFalse($sut->isValidValue(3.14));
        $this->assertFalse($sut->isValidValue('Invalid email'));
    }

    public function testIsValidNull(): void
    {
        $sut = new EmailType();
        $this->assertTrue($sut->isValidValue(null));
    }

    public function testCoerce(): void
    {
        $sut = new EmailType();
        $this->assertNull($sut->coerce(null));
        $this->assertEquals('simpkevin@gmail.com', $sut->coerce('simpkevin@gmail.com'));
    }

    public function testCoerceException(): void
    {
        try {
            $sut = new EmailType();
            $sut->coerce('INVALID');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type.', $e->getMessage());
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
