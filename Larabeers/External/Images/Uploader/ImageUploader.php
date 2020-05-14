<?php

namespace Larabeers\External\Images\Uploader;

use Larabeers\Domain\Common\Image;

interface ImageUploader
{
    public function upload(string $image_path): string;
}
