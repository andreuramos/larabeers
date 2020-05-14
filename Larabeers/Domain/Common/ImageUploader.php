<?php

namespace Larabeers\Domain\Common;

interface ImageUploader
{
    public function upload(string $image_path): string;
}
