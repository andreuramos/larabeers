<?php

namespace Larabeers\Utils\Tests;

use Larabeers\Utils\ResizeImage;
use PHPUnit\Framework\TestCase;

class ResizeImageTest extends TestCase
{
    public function test_resized_image_width()
    {
        $src_image_path = __DIR__ . "/gopnik.jpg";

        $class = new ResizeImage();
        $resized_image_path = $class->execute($src_image_path, ResizeImage::THUMBNAIL_WIDTH);

        $actual_resized_image_width = imagesx(imagecreatefromstring(file_get_contents($resized_image_path)));
        $this->assertEquals(ResizeImage::THUMBNAIL_WIDTH, $actual_resized_image_width);

        unlink($resized_image_path);
    }
}
