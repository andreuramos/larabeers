<?php

namespace Larabeers\Entities;

class Tag
{
    public $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->text;
    }
}
