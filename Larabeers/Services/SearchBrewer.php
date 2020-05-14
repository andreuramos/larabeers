<?php

namespace Larabeers\Services;

use Larabeers\External\EloquentBrewerRepository;
use Larabeers\Utils\NormalizeString;

class SearchBrewer
{
    private $brewer_repository;
    private $normalize_string;

    public function __construct(
        EloquentBrewerRepository $brewer_repository,
        NormalizeString $normalize_string
    )
    {
        $this->brewer_repository = $brewer_repository;
        $this->normalize_string = $normalize_string;
    }

    public function execute(string $query): array
    {
        $results = [];

        $normalized_query = $this->normalize_string->execute($query);
        foreach ($this->brewer_repository->search($normalized_query) as $query_result) {
            $results[] = $query_result;
        }

        return $results;
    }
}
