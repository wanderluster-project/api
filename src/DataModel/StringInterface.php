<?php

declare(strict_types=1);

namespace App\DataModel;

interface StringInterface
{
    public function __toString(): string;

    public function asString(): string;
}
