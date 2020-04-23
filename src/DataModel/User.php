<?php

declare(strict_types=1);

namespace App\DataModel;

class User
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * User constructor.
     *
     * @param string $lang
     */
    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}
