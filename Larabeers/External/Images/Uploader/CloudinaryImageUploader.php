<?php

namespace Larabeers\External\Images\Uploader;

use Cloudinary\Uploader;
use Larabeers\Domain\Common\ImageUploader;

class CloudinaryImageUploader implements ImageUploader
{
    public function upload(string $image_path): string
    {
        \Cloudinary::config([
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
            'secure' => true
        ]);

        $upload = Uploader::upload($image_path);

        return $upload['url'];
    }
}
