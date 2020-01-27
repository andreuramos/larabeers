<?php

namespace Larabeers\Services;

use Larabeers\Entities\Label;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\External\LabelRepository;
use Larabeers\Utils\GetFileType;

class CreateLabelToBeer
{
    const SUPPORTED_MIMES = ['image/jpg'];
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

    public function execute(int $beer_id, $tmp_file_path, array $metadata): void
    {
        if (!$tmp_file_path) throw new \Exception("No image provided");

        $file_type = $this->get_file_type->execute($tmp_file_path);
        if (! in_array($file_type, self::SUPPORTED_MIMES)) {
            throw new UploadFailedException("Unsupported image type");
        }

        $uploaded_image = $this->label_uploader->upload($tmp_file_path);

        $label = new Label();
        $label->beer_id = $beer_id;
        $label->year = $metadata['year'];
        $label->album = $metadata['album'];
        $label->page = $metadata['page'];
        $label->position = $metadata['position'];
        $label->image = $uploaded_image;

        $this->label_repository->save($label);
    }
}
