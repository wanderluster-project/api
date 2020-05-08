<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\IntegerType;
use App\Exception\WanderlusterException;
use PHPUnit\Framework\TestCase;

class IntegerTypeTest extends TestCase implements TypeTestInterface
{
    public function testNullConstructor(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isNull());
    }

    public function testIsNull(): void
    {
        $sut = new IntegerType();
        $this->assertTrue($sut->isNull());

        $sut = new IntegerType(150);
        $this->assertFalse($sut->isNull());
    }

    public function testConstructorWithValue(): void
    {
        $sut = new IntegerType(5);
        $this->assertEquals(5, $sut->getValue());
    }

    public function testTranslations(): void
    {
        // integer doesn't support translations
        $this->assertFalse(false);
    }

    public function testTranslationsException(): void
    {
        // integer doesn't support translations
        $this->assertFalse(false);
    }

    public function testToArray(): void
    {
        // null
        $sut = new IntegerType();
        $this->assertEquals(['val' => null, 'type' => 'INT', 'ver' => 0], $sut->toArray());

        // negative value
        $sut = new IntegerType(-1, ['ver' => 150]);
        $this->assertEquals(['val' => -1, 'type' => 'INT', 'ver' => 150], $sut->toArray());

        // positive value
        $sut = new IntegerType(150, ['ver' => 150]);
        $this->assertEquals(['val' => 150, 'type' => 'INT', 'ver' => 150], $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new IntegerType();
        $sut->fromArray(['val' => null, 'type' => 'INT', 'ver' => 0]);
        $this->assertNull($sut->getValue());

        $sut->fromArray(['type' => 'INT', 'val' => 150, 'ver' => 10]);
        $this->assertEquals(150, $sut->getValue());
        $this->assertEquals(10, $sut->getVersion());
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new IntegerType();
        try {
            $sut->fromArray(['val' => 140]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: type.', $e->getMessage());
        }

        // missing value
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: val.', $e->getMessage());
        }

        // missing ver
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT', 'val' => 150]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Missing Field: ver.', $e->getMessage());
        }

        // invalid value
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'INT', 'val' => 3.14, 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type - Integer required.', $e->getMessage());
        }

        // invalid TYPE
        $sut = new IntegerType();
        try {
            $sut->fromArray(['type' => 'FOO', 'val' => 3, 'ver' => 10]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating INT data type - Invalid Type: FOO.', $e->getMessage());
        }
    }

    public function testSetGet(): void
    {
        $sut = new IntegerType();
        $this->assertNull($sut->getValue());

        $sut->setValue(150);
        $this->assertEquals(150, $sut->getValue());

        $sut->setValue(-1);
        $this->assertEquals(-1, $sut->getValue());
    }

    public function testSetGetNull(): void
    {
        $sut = new IntegerType();
        $this->assertNull($sut->getValue());
        $sut->setValue(null);
        $this->assertNull($sut->getValue());
    }

    public function testSetGetVersion(): void
    {
        $sut = new IntegerType();
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
            $sut = new IntegerType();
            $sut->setValue('I am a string');
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type - Integer required.', $e->getMessage());
        }
    }

    public function testInvalidConstructorValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new IntegerType('I am a string');
            $this->fail('Exception not thrown');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Invalid value passed to INT data type - Integer required.', $e->getMessage());
        }
    }

    public function testGetLanguages(): void
    {
        $sut = new IntegerType();
        $this->assertEquals(['*'], $sut->getLanguages());
    }
}
