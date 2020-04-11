<?php

namespace Larabeers\Services;

use Larabeers\External\TagRepository;

class SearchTag
{
    private $tag_repository;

    public function __construct(TagRepository $tag_repository)
    {
        $this->tag_repository = $tag_repository;
    }

    public function execute(string $query): array
    {
        return $this->tag_repository->search($query);
    }
}
