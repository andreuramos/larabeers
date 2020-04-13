<?php

namespace Larabeers\Services;

use Larabeers\Entities\Beer;
use Larabeers\Entities\Style;
use Larabeers\Exceptions\BrewerNotFoundException;
use Larabeers\Exceptions\DuplicatedBeerException;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\BeerRepository;
use Larabeers\External\BrewerRepository;

class CreateBeer
{
    private $beer_repository;
    private $brewer_repository;

    public function __construct(
        BeerRepository $beer_repository,
        BrewerRepository $brewer_repository
    ) {
        $this->beer_repository = $beer_repository;
        $this->brewer_repository = $brewer_repository;
    }

    public function execute(string $name, int $brewer_id, Style $style): int
    {
        if (!$name) {
            throw new ServiceArgumentException("Name must not be empty");
        }

        $brewer = $this->brewer_repository->findById($brewer_id);
        if (!$brewer) {
            throw new BrewerNotFoundException("Brewer $brewer_id not found");
        }

        if ($this->beer_repository->alreadyExists($name, $brewer)) {
            throw new DuplicatedBeerException();
        }

        $beer = new Beer();
        $beer->brewers[] = $brewer;
        $beer->style = $style;

        $id = $this->beer_repository->save($beer);

        return $id;
    }
}
