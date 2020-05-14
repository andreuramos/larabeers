<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Exceptions\BeerNotFoundException;

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
