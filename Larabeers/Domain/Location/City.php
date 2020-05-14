<?php

namespace Larabeers\Domain\Location;

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
