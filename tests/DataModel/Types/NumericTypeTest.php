<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\NumericType;
use App\Exception\TypeError;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class NumericTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new NumericType();
        $this->assertTrue($sut->isNull());

        $sut = new NumericType(150);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        // integer
        $sut = new NumericType(150);
        $this->assertEquals(150, $sut->getValue());

        // float
        $sut = new NumericType(3.14);
        $this->assertEquals(3.14, $sut->getValue());
    }

    public function testTranslations(): void
    {
        // numeric doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        // null
        $sut = new NumericType();
        $this->assertEquals(['type' => 'NUM', 'val' => null], $sut->toArray());

        // integer
        $sut = new NumericType(150);
        $this->assertEquals(['type' => 'NUM', 'val' => 150], $sut->toArray());

        // float
        $sut = new NumericType(3.14);
        $this->assertEquals(['type' => 'NUM', 'val' => 3.14], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new NumericType();
        $sut->fromArray(['val' => null, 'type' => 'NUM']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'NUM', 'val' => 3.14]);
        $this->assertEquals(3.14, $sut->getValue());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new NumericType();
        try {
            $sut->fromArray(['val' => 140]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Missing Field: type', $e->getMessage());
        }

        // missing value
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'NUM']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating NUM data type - Missing Field: val', $e->getMessage());
        }

        // invalid value
        $sut = new NumericType();
        try {
            $sut->fromArray(['type' => 'NUM', 'val' => 'I AM INVALID']);
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to NUM data type - Numeric required.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->getValue());

        $sut->setValue(150);
        $this->assertEquals(150, $sut->getValue());

        $sut->setValue(-1);
        $this->assertEquals(-1, $sut->getValue());

        $sut->setValue(3.14);
        $this->assertEquals(3.14, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new NumericType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testInvalidSetValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new NumericType('I am a string');
            $this->fail('Exception not thrown');
        } catch (TypeError $e) {
            $this->assertInstanceOf(TypeError::class, $e);
        }

        try {
            $sut = new NumericType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to NUM data type - Numeric required.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new NumericType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }
}
