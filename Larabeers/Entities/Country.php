<?php

namespace Larabeers\Entities;

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
