<?php

namespace Larabeers\Services;

use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Exceptions\LabelNotFoundException;

class DeleteLabel
{
    private $label_repository;

    public function __construct(LabelRepository $label_repository)
    {
        $this->label_repository = $label_repository;
    }

    public function execute(Label $label): void
    {
        if (!$label->id) {
            throw new LabelNotFoundException();
        }

        $this->label_repository->delete($label);
    }
}
