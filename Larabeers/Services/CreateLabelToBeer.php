<?php

namespace Larabeers\Services;

use Larabeers\Entities\Image;
use Larabeers\Entities\Label;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\External\LabelRepository;
use Larabeers\Utils\GetFileType;

class CreateLabelToBeer
{
    private $get_file_type;
    private $label_uploader;
    private $label_repository;

    public function __construct(
        GetFileType $get_file_type,
        ImageUploader $label_uploader,
        LabelRepository $label_repository
    )
    {
        $this->get_file_type = $get_file_type;
        $this->label_uploader = $label_uploader;
        $this->label_repository = $label_repository;
    }

    public function execute(int $beer_id, $tmp_file_path, array $metadata, array $tags = null): void
    {
        if (!$tmp_file_path) throw new \Exception("No image provided");

        $file_type = $this->get_file_type->execute($tmp_file_path);
        if (! in_array($file_type, Image::SUPPORTED_MIMES)) {
            throw new UploadFailedException("Unsupported image type");
        }

        $sticker_url = $this->label_uploader->upload($tmp_file_path);
        $sticker = new Image();
        $sticker->url = $sticker_url;

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
}
