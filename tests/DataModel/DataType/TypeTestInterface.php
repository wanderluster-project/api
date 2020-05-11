<?php

declare(strict_types=1);

namespace App\Tests\DataModel\DataType;

interface TypeTestInterface
{
    public function testNullConstructor(): void;

    public function testIsNull(): void;

    public function testConstructorWithValue(): void;

    public function testTranslations(): void;

    public function testTranslationsException(): void;

    public function testToArray(): void;

    public function testFromArray(): void;

    public function testFromArrayException(): void;

    public function testSetGet(): void;

    public function testSetGetNull(): void;

    public function testSetGetVersion(): void;

    public function testInvalidSetValue(): void;

    public function testInvalidConstructorValue(): void;

    public function testGetLanguages(): void;

    public function testMerge(): void;

    public function testMergeException(): void;
}
