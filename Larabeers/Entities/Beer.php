<?php

namespace Larabeers\Entities;

use Larabeers\External\FlagRepository;

class Beer
{
    public $id;
    public $name;
    public $normalized_name;
    public ?Style $style;
    public $created_at;
    public $brewers;
    public $labels;

    public function flag(): ?string {
        if (!count($this->brewers)) return null;

        return FlagRepository::get($this->brewers[0]->country);
    }
}
