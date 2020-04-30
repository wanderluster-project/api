<?php

declare(strict_types=1);

namespace App\DataModel\Translation;

class LanguageCodes
{
    const ENGLISH = 'en';
    const SPANISH = 'es';

    /**
     * @return string[]
     */
    public function getLanguageCodes(): array
    {
        return [
            self::ENGLISH,
            self::SPANISH,
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
