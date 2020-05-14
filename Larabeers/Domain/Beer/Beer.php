<?php

namespace Larabeers\Domain\Beer;

class Beer
{
    public $id;
    public $name;
    public $normalized_name;
    public ?Style $style;
    public $created_at;
    public $brewers;
    public $labels;
}
