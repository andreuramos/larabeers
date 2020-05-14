<?php

namespace Larabeers\Services;

use Larabeers\Domain\Beer\StyleRepository;

class SearchStyle
{
    private $style_repository;

    public function __construct(StyleRepository $style_repository)
    {
        $this->style_repository = $style_repository;
    }

    public function execute(string $style_name): array
    {
        return $this->style_repository->search($style_name);
    }
}
