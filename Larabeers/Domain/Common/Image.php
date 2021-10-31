<?php

namespace Larabeers\Domain\Common;

class Image
{
    //@TODO: make private with method to check supported or not
    public const SUPPORTED_MIMES = ['image/jpg', 'image/jpeg', 'image/png'];

    public $url;
    public $thumbnail;
    public $small;

    public function thumbnail(): string
    {
        if ($this->thumbnail) {
            return $this->thumbnail;
        }
        if ($this->small) {
            return $this->small;
        }
        return $this->url;
    }
}
