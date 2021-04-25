<?php

namespace Larabeers\Domain\Location;

use Larabeers\External\Images\FlagpediaFlagRepository;

class Country
{
    public $name;
    public $flag;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->flag = FlagpediaFlagRepository::get($name);
    }
}
