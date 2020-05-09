<?php

declare(strict_types=1);

namespace App\DataModel\Translation;

class LanguageCodes
{
    const ANY = '*';
    const ENGLISH = 'en';
    const SPANISH = 'es';
    const FRENCH = 'fr';
    const CHINESE = 'zh';
    const JAPANES = 'ja';

    /**
     * @return string[]
     */
    public function getLanguageCodes(): array
    {
        return [
            self::ANY,
            self::ENGLISH,
            self::SPANISH,
            self::FRENCH,
            self::CHINESE,
            self::JAPANES,
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
