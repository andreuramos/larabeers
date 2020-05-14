<?php

namespace Larabeers\Domain\Label;

interface LabelRepository
{
    public function findById(int $id): ?Label;
    public function save(Label $label): int;

}
