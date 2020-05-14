<?php

namespace Larabeers\Domain\Beer;

use Larabeers\Domain\Brewer\Brewer;

interface BeerRepository
{
    public function findById(int $id): ?Beer;
    public function save(Beer $beer): int;
    public function findByCriteria(BeerCriteria $criteria): array;
    public function random(int $limit): array;
    public function exists(string $name, Brewer $brewer): bool;
}
