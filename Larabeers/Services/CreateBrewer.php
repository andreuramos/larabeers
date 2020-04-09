<?php

namespace Larabeers\Services;

use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\BrewerRepository;
use Larabeers\Utils\NormalizeString;

class CreateBrewer
{
    private $brewer_repository;
    private $normalize_string;

    public function __construct(
        BrewerRepository $brewer_repository,
        NormalizeString $normalize_string
    ) {
        $this->brewer_repository = $brewer_repository;
        $this->normalize_string = $normalize_string;
    }

    public function execute(string $name, City $city)
    {
        if ($name === "") {
            throw new ServiceArgumentException("name parameter must not be empty");
        }

        $brewer = new Brewer();
        $brewer->name = $name;
        $brewer->city = $city;
        $brewer->normalized_name = $this->normalize_string->execute($name);

        $id = $this->brewer_repository->save($brewer);

        return $id;
    }
}
