<?php

namespace Larabeers\Services;

use Larabeers\Domain\Common\Image;
use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Domain\Label\Tag;
use Larabeers\Domain\Label\TagRepository;
use Larabeers\Exceptions\LabelNotFoundException;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\Domain\Common\ImageUploader;
use Larabeers\Utils\GetFileType;
use Larabeers\Utils\ResizeImage;

class UpdateLabel
{
    private $label_repository;
    private $get_file_type;
    private $image_uploader;
    //@TODO: remove this dependency
    private $tag_repository;
    private $resize_image;

    public function __construct(
        LabelRepository $label_repository,
        GetFileType $get_file_type,
        ImageUploader $image_uploader,
        TagRepository $tag_repository,
        ResizeImage $resize_image
    ) {
        $this->label_repository = $label_repository;
        $this->get_file_type = $get_file_type;
        $this->image_uploader = $image_uploader;
        $this->tag_repository = $tag_repository;
        $this->resize_image = $resize_image;
    }

    public function execute(
        int $label_id,
        ?string $path_to_file,
        array $metadata = null,
        array $tags = null
    ): Label {
        $label = $this->label_repository->findById($label_id);
        if ($label == null) {
            throw new LabelNotFoundException("Label $label_id not found");
        }

        if ($path_to_file !== null) {
            $sticker = $this->uploadSticker($path_to_file);
            $label->sticker = $sticker;
        }

        if ($metadata !== null) {
            $label->year = array_key_exists('year', $metadata) ? $metadata['year'] : $label->year;
            $label->album = array_key_exists('album', $metadata) ? $metadata['album'] : $label->album;
            $label->page = array_key_exists('page', $metadata) ? $metadata['page'] : $label->page;
            $label->position = array_key_exists('position', $metadata) ? $metadata['position'] : $label->position;
        }

        if ($tags !== null) {
            foreach ($tags as $i => $tag) {
                if (!is_a($tag, Tag::class)) {
                    throw new ServiceArgumentException("Tag $i is not a \Larabeers\Domain\Label\Tag object");
                }
            }
            $label->tags = $tags;
        }

        $this->label_repository->save($label);
        return $label;
    }

    private function checkFileType(string $path_to_file): void
    {
        $file_type = $this->get_file_type->execute($path_to_file);
        if (!in_array($file_type, Image::SUPPORTED_MIMES)) {
            throw new UploadFailedException("File type $file_type not supported");
        }
    }

    private function uploadSticker(?string $path_to_file): Image
    {
        $this->checkFileType($path_to_file);
        $image_url = $this->image_uploader->upload($path_to_file);

        $thumbnail_path = $this->resize_image->execute($path_to_file, ResizeImage::THUMBNAIL_WIDTH);
        $thumbnail_url = $this->image_uploader->upload($thumbnail_path);

        $sticker = new Image();
        $sticker->url = $image_url;
        $sticker->thumbnail = $thumbnail_url;

        return $sticker;
    }
}
