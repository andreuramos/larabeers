<?php

namespace Larabeers\Domain\Beer;

use Larabeers\Domain\Common\ItemCollection;

class BeerCollection extends ItemCollection
{
    public function add(Beer $beer)
    {
        $this->items[] = $beer;
    }

    public function first(): Beer
    {
        return $this->items[0];
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
