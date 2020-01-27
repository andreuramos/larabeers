<?php

namespace Larabeers\Entities;

class Label
{
    public Image $image;

    public int $id;
    public int $beer_id;

    public int $year;
    public int $month;

    public int $album;
    public int $page;
    public int $position;

    public function get_image_url(): string
    {
        return $this->image->url;
    }


}
