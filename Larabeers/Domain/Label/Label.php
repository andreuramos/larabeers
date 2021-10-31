<?php

namespace Larabeers\Domain\Label;

use Larabeers\Domain\Common\Image;

class Label
{
    public ?Image $sticker;

    public ?int $id;
    public int $beer_id;

    public ?int $year;
    public int $month;

    public int $album;
    public int $page;
    public int $position;

    public array $tags;

    public function __construct()
    {
        $this->id = null;
    }

    public function getImageUrl(): string
    {
        return $this->sticker->url;
    }
}
