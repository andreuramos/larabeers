<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\Style;
use Larabeers\Exceptions\BrewerNotFoundException;
use Larabeers\Exceptions\DuplicatedBeerException;
use Larabeers\Exceptions\ServiceArgumentException;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\External\BrewerRepository;
use Larabeers\Utils\NormalizeString;

class CreateBeer
{
    private $beer_repository;
    private $brewer_repository;
    private $normalize_string;

    public function __construct(
        EloquentBeerRepository $beer_repository,
        BrewerRepository $brewer_repository,
        NormalizeString $normalize_string
    ) {
        $this->beer_repository = $beer_repository;
        $this->brewer_repository = $brewer_repository;
        $this->normalize_string = $normalize_string;
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

        if ($this->beer_repository->exists($name, $brewer)) {
            throw new DuplicatedBeerException();
        }

        $beer = new Beer();
        $beer->name = $name;
        $beer->brewers[] = $brewer;
        $beer->style = $style;
        $beer->normalized_name = $this->normalize_string->execute($name);

        $id = $this->beer_repository->save($beer);

        return $id;
    }
}
