<?php

namespace Larabeers\Domain\Beer;

class BeerCriteria
{
    private $order;
    private $limit;

    public function __construct()
    {
        $this->order = [];
        $this->limit = null;
    }

    public function addOrder(string $attribute)
    {
        $this->order[] = $attribute;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function addLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
