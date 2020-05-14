<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\Style;
use Larabeers\Exceptions\BeerNotFoundException;
use Larabeers\Exceptions\BrewerNotFoundException;
use Larabeers\External\BeerRepository;
use Larabeers\External\BrewerRepository;
use Larabeers\Utils\NormalizeString;

class UpdateBeer
{
    private $beer_repository;
    private $brewer_repository;
    private $normalize_string;

    public function __construct(
        BeerRepository $beer_repostiory,
        BrewerRepository $brewer_repository,
        NormalizeString $normalize_string
    ) {
        $this->beer_repository = $beer_repostiory;
        $this->brewer_repository = $brewer_repository;
        $this->normalize_string = $normalize_string;
    }

    public function execute(int $id, string $name, int $brewer_id, Style $style): void
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            throw new BeerNotFoundException("beer $id not found");
        }

        if ($beer->name != $name) {
            $beer->name = $name;
            $beer->normalized_name = $this->normalize_string->execute($name);
        }

        $brewer = $this->brewer_repository->findById($brewer_id);
        if (!$brewer) {
            throw new BrewerNotFoundException("Brewer $brewer_id not found while updating beer $id");
        }
        $beer->brewers[0] = $brewer;

        $beer->style = $style;

        $this->beer_repository->save($beer);
    }
}
