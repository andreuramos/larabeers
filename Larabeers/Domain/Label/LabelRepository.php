<?php

namespace Larabeers\Domain\Label;

interface LabelRepository
{
    public function findById(int $id): ?Label;
    public function save(Label $label): int;
    public function delete(Label $label): void;
}
