<?php

namespace Larabeers\Domain\Brewer;

interface BrewerRepository
{
    public function findById(int $id): ?Brewer;
    public function save(Brewer $brewer): int;
    public function search(string $query): array;
}
