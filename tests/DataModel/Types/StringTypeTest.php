<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Types;

use App\DataModel\Types\StringType;
use App\Exception\TypeError;
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

    public function testToArray(): void
    {
        $sut = new StringType(['en' => 'The quick brown fox jumps over the lazy dog', 'es' => 'El rápido zorro marrón salta sobre el perro perezoso']);
        $this->assertEquals(
            [
                'type' => 'STRING',
                'trans' => [
                    'en' => 'The quick brown fox jumps over the lazy dog',
                    'es' => 'El rápido zorro marrón salta sobre el perro perezoso',
                ],
            ],
            $sut->toArray());
    }

    public function testFromArray(): void
    {
        $sut = new StringType();
        $this->assertTrue($sut->isNull(['lang' => 'en']));
        $sut->fromArray([
            'type' => 'STRING',
            'trans' => [
                'en' => 'The quick brown fox jumps over the lazy dog',
                'es' => 'El rápido zorro marrón salta sobre el perro perezoso',
            ],
        ]);
        $this->assertEquals('The quick brown fox jumps over the lazy dog', $sut->getValue(['lang' => 'en']));
        $this->assertEquals('El rápido zorro marrón salta sobre el perro perezoso', $sut->getValue(['lang' => 'es']));
    }

    public function testFromArrayException(): void
    {
        // missing type
        $sut = new StringType();
        try {
            $sut->fromArray([]);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Missing Field: type', $e->getMessage());
        }

        // missing value
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'STRING']);
            $this->fail('Exception not thrown.');
        } catch (WanderlusterException $e) {
            $this->assertEquals('Error hydrating STRING data type - Missing Field: trans', $e->getMessage());
        }

        // invalid value
        $sut = new StringType();
        try {
            $sut->fromArray(['type' => 'STRING', 'trans' => 'I AM INVALID']);
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Error hydrating STRING data type - trans should be an array.', $e->getMessage());
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
    }

    public function testSetGetNull(): void
    {
        $sut = new StringType();
        $this->assertNull($sut->getValue(['lang' => 'es']));
        $sut->setValue(null, ['lang' => 'es']);
        $this->assertNull($sut->getValue(['lang' => 'es']));
    }

    public function testInvalidSetValue(): void
    {
        try {
            // @phpstan-ignore-next-line
            $sut = new StringType(['en' => 123]);
            $this->fail('Exception not thrown');
        } catch (TypeError $e) {
            $this->assertInstanceOf(TypeError::class, $e);
        }

        try {
            $sut = new StringType();
            $sut->setValue(123, ['lang' => 'en']);
            $this->fail('Exception not thrown.');
        } catch (TypeError $e) {
            $this->assertEquals('Invalid value passed to STRING data type - String required.', $e->getMessage());
        }
    }
}
