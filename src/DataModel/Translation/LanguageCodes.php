<?php

declare(strict_types=1);

namespace App\DataModel\Translation;

class LanguageCodes
{
    const ANY = '*';
    const ENGLISH = 'en';
    const SPANISH = 'es';
    const FRENCH = 'fr';

    /**
     * @return string[]
     */
    public function getLanguageCodes(): array
    {
        return [
            self::ENGLISH,
            self::SPANISH,
            self::FRENCH,
        ];
    }

    /**
     * @param string $languageCode
     */
    public function isValidLanguageCode($languageCode): bool
    {
        return in_array($languageCode, $this->getLanguageCodes());
    }
}
