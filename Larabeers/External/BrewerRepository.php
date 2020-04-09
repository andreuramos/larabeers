<?php

namespace Larabeers\External;

use App\Brewer as EloquentBrewer;
use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Entities\Country;

class BrewerRepository
{
    public function findById(int $id): ?Brewer
    {
        $eloquent_brewer = EloquentBrewer::find($id);
        if (!$eloquent_brewer) return null;

        return $this->eloquentToEntityBrewer($eloquent_brewer);
    }

    public function save(Brewer $brewer): int
    {
        if ($brewer->id) {
            $eloquent_brewer = EloquentBrewer::find($brewer->id);
        } else {
            $eloquent_brewer = new EloquentBrewer();
        }

        $eloquent_brewer->name = $brewer->name;
        $eloquent_brewer->normalized_name = $brewer->normalized_name;
        $eloquent_brewer->country = $brewer->city->country->name;
        $eloquent_brewer->city = $brewer->city->name;

        $eloquent_brewer->save();

        return $eloquent_brewer->id;
    }

    private function eloquentToEntityBrewer(EloquentBrewer $eloquent_brewer): Brewer
    {
        $brewer = new Brewer();
        $brewer->id = $eloquent_brewer->id;
        $brewer->name = $eloquent_brewer->name;
        $brewer->normalized_name = $eloquent_brewer->normalized_name;
        $country = new Country($eloquent_brewer->country);
        $brewer->city = new City($eloquent_brewer->city, $country);

        return $brewer;
    }

    public function search(string $query): array
    {
        $results = [];
        $db_results = EloquentBrewer::where('normalized_name', 'ilike', "%$query%")->get();
        foreach ($db_results as $db_result) {
            $results[] = $this->eloquentToEntityBrewer($db_result);
        }

        return $results;
    }

}
