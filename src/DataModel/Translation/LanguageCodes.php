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
}
