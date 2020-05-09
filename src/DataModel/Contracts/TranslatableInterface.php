<?php

declare(strict_types=1);

namespace App\DataModel\Contracts;

interface TranslatableInterface extends SerializableInterface, VersionableInterface
{
    /**
     * Set the language for this translation.
     */
    public function setLanguage(string $lang): TranslatableInterface;

    /**
     * Get the language for this translation.
     */
    public function getLanguage(): string;

    /**
     * Set the value for this translation.
     */
    public function setValue(string $val): TranslatableInterface;

    /**
     * Get the value for this translation.
     */
    public function getValue(): string;
}
