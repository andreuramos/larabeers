<?php

namespace Larabeers\Services;

use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Location\City;
use Larabeers\Domain\Common\Image;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\BrewerRepository;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\Utils\NormalizeString;

class CreateBrewer
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

    public function execute(string $name, City $city, ?array $data = null): int
    {
        if ($name === "") {
            throw new ServiceArgumentException("name parameter must not be empty");
        }

        $brewer = new Brewer();
        $brewer->name = $name;
        $brewer->city = $city;
        $brewer->normalized_name = $this->normalize_string->execute($name);

        if ($data !== null) {
            $brewer = $this->set_brewer_data($brewer, $data);
        }

        $id = $this->brewer_repository->save($brewer);

        return $id;
    }

    private function set_brewer_data(Brewer $brewer, array $data): Brewer
    {
        if (array_key_exists('address', $data)) {
            $brewer->address = $data['address'];
        }

        if (array_key_exists('lat', $data) && array_key_exists('lng', $data)) {
            $brewer->latitude = $data['lat'];
            $brewer->longitude = $data['lng'];
        }

        if (array_key_exists('website', $data)) {
            $brewer->website = $data['website'];
        }

        if (array_key_exists('logo', $data)) {
            $logo = $this->create_logo($data['logo']);
            $brewer->logo = $logo;
        }

        return $brewer;
    }

    private function create_logo($logo): Image
    {
        $url = $this->image_uploader->upload($logo);
        $logo = new Image();
        $logo->url = $url;
        return $logo;
    }
}
