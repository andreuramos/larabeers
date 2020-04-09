<?php

namespace Larabeers\Services;

use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\BrewerRepository;

class CreateBrewer
{
    private $brewer_repository;

    public function __construct(BrewerRepository $brewer_repository)
    {
        $this->brewer_repository = $brewer_repository;
    }

    public function execute(string $name, City $city)
    {
        if ($name === "" || $name === null) {
            throw new ServiceArgumentException("name parameter must not be empty");
        }
        $brewer = new Brewer();
        $brewer->name = $name;
        $brewer->city = $city;

        $id = $this->brewer_repository->save($brewer);

        return $id;
    }
}
