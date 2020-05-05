<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\EmailType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class EmailTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new EmailType();
        $this->assertEquals('EMAIL', $sut->getTypeId());
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
        $this->assertEquals(['val' => null, 'type' => 'EMAIL'], $sut->toArray());

        $sut = new EmailType('simpkevin@gmail.com');
        $this->assertEquals(['val' => 'simpkevin@gmail.com', 'type' => 'EMAIL'], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new EmailType();
        $sut->fromArray(['val' => null, 'type' => 'EMAIL']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'EMAIL', 'val' => 'simpkevin@gmail.com']);
        $this->assertEquals('simpkevin@gmail.com', $sut->getValue());
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
            $sut->fromArray(['type' => 'EMAIL', 'val' => 3.14]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type - String required.', $e->getMessage());
        }

        // invalid value
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'EMAIL', 'val' => 'simpkevin']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type - Invalid Email.', $e->getMessage());
        }

        // invalid type
        $sut = new EmailType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 'simpkevin@gmail.com']);
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

    public function testInvalidSetValue(): void
    {
        try {
            $sut = new EmailType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type - Invalid Email.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            $sut = new EmailType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to EMAIL data type - Invalid Email.', $e->getMessage());
        }
    }


    public function testGetLanguages(): void
    {
        $sut = new EmailType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }
}
