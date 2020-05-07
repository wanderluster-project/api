<?php

declare(strict_types=1);

namespace App\Tests\DataModel\Translations;

use App\DataModel\Translation\LanguageCodes;
use PHPUnit\Framework\TestCase;

class LanguageCodesTest extends TestCase
{
    public function testGetLanguageCodes(): void
    {
        $sut = new LanguageCodes();
        $this->assertEquals(['*', 'en', 'es', 'fr'], $sut->getLanguageCodes());
    }

    public function testIsValidTestCode(): void
    {
        $sut = new LanguageCodes();
        $this->assertTrue($sut->isValidLanguageCode('en'));
        $this->assertTrue($sut->isValidLanguageCode('es'));
        $this->assertTrue($sut->isValidLanguageCode('fr'));
    }
}
