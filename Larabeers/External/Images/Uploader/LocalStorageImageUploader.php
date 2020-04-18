<?php

namespace Larabeers\External\Images\Uploader;

class LocalStorageImageUploader implements ImageUploader
{
    private $upload_path;

    public function __construct()
    {
        $this->upload_path = public_path('upload');
    }

    public function upload(string $image_path): string
    {
        $file = file_get_contents($image_path);
        $file_name = uniqid("image");
        file_put_contents($this->upload_path . '/' . $file_name, $file);

        return '/upload/' . $file_name;
    }
}
