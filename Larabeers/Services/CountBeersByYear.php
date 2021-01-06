<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Common\Year;

class CountBeersByYear
{
    private $beer_repository;

    public function __construct(BeerRepository $beer_repository)
    {
        $this->beer_repository = $beer_repository;
    }

    public function execute(Year $year): int
    {
        return $this->beer_repository->countByYear($year->getYear());
    }
}
