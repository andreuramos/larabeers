<?php

namespace Larabeers\Entities;

class Style
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
