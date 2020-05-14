<?php

namespace Larabeers\Domain\Beer;

class Style
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
