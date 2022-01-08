<?php

namespace Larabeers\Domain\Label;

class Tag
{
    public $text;
    public $id;

    public function __construct(string $text, int $id = null)
    {
        $this->text = $text;
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->text;
    }
}
