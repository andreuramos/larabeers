<?php

namespace Larabeers\Domain\Common;

use Countable;
use Iterator;

abstract class ItemCollection implements Iterator, Countable
{
    protected $items;
    private $position;

    public function __construct()
    {
        $this->items = [];
        $this->position = 0;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
        return $this->position;
    }

    public function valid()
    {
        return array_key_exists($this->position, $this->items);
    }

    public function count()
    {
        return count($this->items);
    }
}
