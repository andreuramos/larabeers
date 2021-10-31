<?php

namespace Larabeers\Services;

use Larabeers\Domain\Common\Image;
use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\Domain\Common\ImageUploader;
use Larabeers\Utils\GetFileType;
use Larabeers\Utils\ResizeImage;

class CreateLabelToBeer
{
    private $get_file_type;
    private $image_uploader;
    private $label_repository;
    private $resize_image;

    public function __construct(
        GetFileType $get_file_type,
        ImageUploader $image_uploader,
        LabelRepository $label_repository,
        ResizeImage $resize_image
    ) {
        $this->get_file_type = $get_file_type;
        $this->image_uploader = $image_uploader;
        $this->label_repository = $label_repository;
        $this->resize_image = $resize_image;
    }

    public function execute(int $beer_id, string $tmp_file_path, array $metadata, array $tags = null): void
    {
        if (!$tmp_file_path) {
            throw new \Exception("No image provided");
        }

        $file_type = $this->get_file_type->execute($tmp_file_path);
        if (! in_array($file_type, Image::SUPPORTED_MIMES)) {
            throw new UploadFailedException("Unsupported image type: $file_type");
        }

        $sticker = $this->uploadSticker($tmp_file_path);

        $label = new Label();
        $label->beer_id = $beer_id;
        $label->year = $metadata['year'];
        $label->album = $metadata['album'];
        $label->page = $metadata['page'];
        $label->position = $metadata['position'];
        $label->sticker = $sticker;

        if ($tags !== null) {
            $label->tags = $tags;
        }

        $this->label_repository->save($label);
    }

    private function uploadSticker(string $tmp_file_path): Image
    {
        $large_path = $this->resize_image->execute($tmp_file_path, ResizeImage::LARGE_WIDTH);
        $sticker_url = $this->image_uploader->upload($large_path);

        $thumbnail_path = $this->resize_image->execute($tmp_file_path, ResizeImage::THUMBNAIL_WIDTH);
        $thumbnail_url = $this->image_uploader->upload($thumbnail_path);

        $sticker = new Image();
        $sticker->url = $sticker_url;
        $sticker->thumbnail = $thumbnail_url;

        return $sticker;
    }
}
