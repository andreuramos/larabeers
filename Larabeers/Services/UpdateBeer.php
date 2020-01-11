<?php

namespace Larabeers\Services;

use Larabeers\Exceptions\BeerNotFoundException;
use Larabeers\External\BeerRepository;
use Larabeers\Utils\NormalizeString;

class UpdateBeer
{
    private $beer_repository;
    private $normalize_string;

    public function __construct(
        BeerRepository $beer_repostiory,
        NormalizeString $normalize_string
    ) {
        $this->beer_repository = $beer_repostiory;
        $this->normalize_string = $normalize_string;
    }

    public function execute(int $id, string $name): void
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            throw new BeerNotFoundException("beer $id not found");
        }

        if ($beer->name != $name) {
            $beer->name = $name;
            $beer->normalized_name = $this->normalize_string->execute($name);
        }

        $this->beer_repository->save($beer);
    }
}
