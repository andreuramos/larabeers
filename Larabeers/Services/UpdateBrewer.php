<?php

namespace Larabeers\Services;

use Larabeers\Entities\City;
use Larabeers\Exceptions\BrewerNotFoundException;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\BrewerRepository;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\Utils\NormalizeString;

class UpdateBrewer
{
    private $brewer_repository;
    private $image_uploader;
    private $normalize_string;

    public function __construct(
        BrewerRepository $brewer_repository,
        ImageUploader $image_uploader,
        NormalizeString $normalize_string
    ) {
        $this->brewer_repository = $brewer_repository;
        $this->image_uploader = $image_uploader;
        $this->normalize_string = $normalize_string;
    }

    public function execute(int $id, string $name, City $city, ?array $data = null)
    {
        if (!$name) {
            throw new ServiceArgumentException("Name cannot be empty");
        }

        $brewer = $this->brewer_repository->findById($id);
        if (!$brewer) {
            throw new BrewerNotFoundException("brewer $id does not exist");
        }
    }
}
