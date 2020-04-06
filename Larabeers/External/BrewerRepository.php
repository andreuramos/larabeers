<?php

namespace Larabeers\External;

use App\Brewer as EloquentBrewer;
use Larabeers\Entities\Brewer;

class BrewerRepository
{
    public function findById(int $id): ?Brewer
    {
        $eloquent_brewer = EloquentBrewer::find($id);
        if(!$eloquent_brewer) return null;

        return self::eloquentToEntityBrewer($eloquent_brewer);
    }

    private function eloquentToEntityBrewer(EloquentBrewer $eloquent_brewer): Brewer
    {
        $brewer = new Brewer();
        $brewer->id = $eloquent_brewer->id;
        $brewer->name = $eloquent_brewer->name;
        $brewer->country = $eloquent_brewer->country;

        return $brewer;
    }

}
