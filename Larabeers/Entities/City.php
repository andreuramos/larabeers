<?php

namespace Larabeers\Entities;

class City
{
    public $name;
    public $country;

    public function __construct(string $name, Country $country)
    {
        $this->name = $name;
        $this->country = $country;
    }
}
