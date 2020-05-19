<?php

namespace Larabeers\Domain\Beer;

class Style
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __get($name)
    {
        if ($name == 'name') return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
