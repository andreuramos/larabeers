<?php

namespace Larabeers\Entities;

class Image
{
    const SUPPORTED_MIMES = ['image/jpg', 'image/jpeg'];

    public $url;
    public $thumbnail;
    public $small;

    public function thumbnail(): string
    {
        if ($this->thumbnail) return $this->thumbnail;
        if ($this->small) return $this->small;
        return $this->url;
    }
}
