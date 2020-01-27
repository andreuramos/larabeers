<?php

namespace Larabeers\External\Images\Uploader;

use Larabeers\Entities\Image;

interface ImageUploader
{
    public function upload($image): Image;
}
