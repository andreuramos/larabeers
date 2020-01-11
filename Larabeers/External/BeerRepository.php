<?php

namespace Larabeers\External;

use Larabeers\Entities\Beer;
use App\Beer as EloquentBeer;

class BeerRepository
{
    public function findById(int $id): ?Beer
    {
        $beer = EloquentBeer::find($id);
        return self::eloquentToEntityBeer($beer);
    }

    public function save(Beer $beer): void
    {
        if ($beer->id) {
            $eloquent_beer = EloquentBeer::find($beer->id);
            $eloquent_beer = self::populateEloquentBeer($eloquent_beer, $beer);
        } else {
            $eloquent_beer = self::entityToEloquentBeer($beer);
        }

        $eloquent_beer->save();
    }

    private static function eloquentToEntityBeer(EloquentBeer $eloquent_beer): Beer
    {
        $beer = new Beer();
        $beer->id = $eloquent_beer->id;
        $beer->name = $eloquent_beer->name;
        $beer->normalized_name = $eloquent_beer->normalized_name;
        $beer->type = $eloquent_beer->type;

        return $beer;
    }

    private static function entityToEloquentBeer(Beer $beer): EloquentBeer
    {
        $eloquent_beer = new EloquentBeer();
        $eloquent_beer->id = $beer->id;
        $eloquent_beer->name = $beer->name;
        $eloquent_beer->normalized_name = $beer->normalized_name;
        $eloquent_beer->type = $beer->type;

        return $eloquent_beer;
    }

    private static function populateEloquentBeer(EloquentBeer $eloquent_beer, Beer $beer): EloquentBeer
    {
        $eloquent_beer->id = $beer->id;
        $eloquent_beer->name = $beer->name;
        $eloquent_beer->normalized_name = $beer->normalized_name;
        $eloquent_beer->type = $beer->type;

        return $eloquent_beer;
    }
}
