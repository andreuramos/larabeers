<?php

namespace Larabeers\Domain\Location;

use Larabeers\External\FlagRepository;

class Country
{
    public $name;
    public $flag;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->flag = FlagRepository::get($name);
    }
}
