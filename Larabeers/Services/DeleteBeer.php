<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\Beer;
use Larabeers\Exceptions\BeerNotFoundException;
use Larabeers\External\EloquentBeerRepository;

class DeleteBeer
{
    private EloquentBeerRepository $beer_repository;

    public function __construct(EloquentBeerRepository $beer_repository)
    {
        $this->beer_repository = $beer_repository;
    }

    public function execute(Beer $beer): void
    {
        if (!$beer->id) {
            throw new BeerNotFoundException("Cannot delete non-saved beer");
        }

        $this->beer_repository->delete($beer);
    }
}
