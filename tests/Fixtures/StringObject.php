<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

class StringObject
{
    /**
     * @var string string
     */
    protected $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
