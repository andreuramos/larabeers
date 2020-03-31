<?php

namespace Larabeers\Entities;

class Label
{
    public Image $sticker;

    public ?int $id;
    public int $beer_id;

    public int $year;
    public int $month;

    public int $album;
    public int $page;
    public int $position;

    public function __construct()
    {
        $this->id = null;
    }

    public function get_image_url(): string
    {
        return $this->sticker->url;
    }


}
