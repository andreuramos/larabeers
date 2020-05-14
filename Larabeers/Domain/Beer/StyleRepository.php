<?php

namespace Larabeers\Domain\Beer;

interface StyleRepository
{
    public function search(string $query): array;
}
