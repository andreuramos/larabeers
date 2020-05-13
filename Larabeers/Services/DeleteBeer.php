<?php

namespace Larabeers\Services;

use Larabeers\Entities\Beer;
use Larabeers\Exceptions\BeerNotFoundException;
use Larabeers\External\BeerRepository;

class DeleteBeer
{
    private BeerRepository $beer_repository;

    public function __construct(BeerRepository $beer_repository)
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
