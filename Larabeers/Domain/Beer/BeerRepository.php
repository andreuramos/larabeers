<?php

namespace Larabeers\Domain\Beer;

use Larabeers\Domain\Brewer\Brewer;

interface BeerRepository
{
    public function findById(int $id): ?Beer;
    public function save(Beer $beer): int;
    public function findByCriteria(BeerCriteria $criteria): BeerCollection;
    public function findByBrewerId(int $id): BeerCollection;
    public function search(string $name): BeerCollection;
    public function random(int $limit): BeerCollection;
    public function exists(string $name, Brewer $brewer): bool;
    public function delete(Beer $beer): void;
    public function findByYear(int $year): BeerCollection;
    public function countByYear(int $year): int;
}
