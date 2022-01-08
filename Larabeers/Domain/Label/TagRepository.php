<?php

namespace Larabeers\Domain\Label;

interface TagRepository
{
    public function search(string $query): array;
    public function findById(int $id): ?Tag;
}
