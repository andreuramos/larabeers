<?php

namespace Larabeers\Utils;

class ResizeImage
{
    const THUMBNAIL_WIDTH = 50;

    public function execute(string $src_image_path, int $width): string
    {
        list($src_width, $src_height) = getimagesize($src_image_path);
        $ratio = $src_width / $src_height;
        $height = $width / $ratio;

        $src_file = imagecreatefromstring(file_get_contents($src_image_path));
        $dst = imagecreatetruecolor($width, $height);
        imagecopyresized($dst, $src_file, 0, 0, 0, 0, $width,$height,$src_width,$src_height);

        $dst_image_path = 'public/upload/' . uniqid();
        imagepng($dst, $dst_image_path);

        return $dst_image_path;
    }
}
