<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\BooleanType;
use App\Exception\TypeError;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class BooleanTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new BooleanType();
        $this->assertEquals('BOOL', $sut->getTypeId());
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new BooleanType();
        $this->assertTrue($sut->isNull());

        $sut = new BooleanType(true);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new BooleanType(true);
        $this->assertTrue($sut->getValue());

        $sut = new BooleanType(false);
        $this->assertFalse($sut->getValue());
    }

    public function testTranslations(): void
    {
        // boolean doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        $sut = new BooleanType();
        $this->assertEquals(['val' => null, 'type' => 'BOOL'], $sut->toArray());

        $sut = new BooleanType(true);
        $this->assertEquals(['val' => true, 'type' => 'BOOL'], $sut->toArray());

        $sut = new BooleanType(false);
        $this->assertEquals(['val' => false,  'type' => 'BOOL'], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new BooleanType();
        $sut->fromArray(['val' => null, 'type' => 'BOOL']);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'BOOL', 'val' => true]);
        $this->assertTrue($sut->getValue());

        $sut->fromArray(['type' => 'BOOL', 'val' => false]);
        $this->assertFalse($sut->getValue());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new BooleanType();
        try {
            $sut->fromArray(['val' => true]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating BOOL data type - Missing Field: type', $e->getMessage());
        }

        // missing value
        $sut = new BooleanType();
        try {
            $sut->fromArray(['type' => 'BOOL']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating BOOL data type - Missing Field: val', $e->getMessage());
        }

        // invalid value
        $sut = new BooleanType();
        try {
            $sut->fromArray(['type' => 'BOOL', 'val' => 'I am invalid']);
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to BOOL data type - Boolean required.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->getValue());

        $sut->setValue(true);
        $this->assertTrue($sut->getValue());

        $sut->setValue(false);
        $this->assertFalse($sut->getValue());

        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new BooleanType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testInvalidSetValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new BooleanType('I am a string');
            $this->fail('Exception not thrown');
        } catch (TypeError $e) {
            $this->assertInstanceOf(TypeError::class, $e);
        }

        try {
            $sut = new BooleanType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to BOOL data type - Boolean required.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new BooleanType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }
}
