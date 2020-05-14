<?php

namespace Larabeers\External;

use App\Beer as EloquentBeer;
use App\Brewer as EloquentBrewer;
use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\BeerCriteria;
use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Beer\Style;

class EloquentBeerRepository implements BeerRepository
{
    private BrewerRepository $brewer_repository;
    private LabelRepository $label_repository;

    public function __construct(
        BrewerRepository $brewer_repository,
        LabelRepository $label_repository
    ) {
        $this->brewer_repository = $brewer_repository;
        $this->label_repository = $label_repository;
    }

    public function findById(int $id): ?Beer
    {
        $beer = EloquentBeer::find($id);
        if (!$beer) {
            return null;
        }
        return $this->eloquentToEntityBeer($beer);
    }

    public function save(Beer $beer): int
    {
        if ($beer->id) {
            $eloquent_beer = EloquentBeer::find($beer->id);
            $eloquent_beer = $this->populateEloquentBeer($eloquent_beer, $beer);
        } else {
            $eloquent_beer = $this->entityToEloquentBeer($beer);
            $eloquent_beer->created_at = date_create()->format('Y-m-d H:i:s');
        }

        $eloquent_beer->save();
        $this->saveBrewer($eloquent_beer, $beer->brewers[0]);

        return $eloquent_beer->id;
    }

    public function findByCriteria(BeerCriteria $criteria): array
    {
        $results = [];
        $query = EloquentBeer::query();

        $order = $criteria->getOrder();
        if (!empty($order)) {
            foreach($order as $order_attribute) {
                $query->orderBy($order_attribute, 'desc');
            }
        }

        $limit = $criteria->getLimit();
        if($limit) {
            $query->limit($limit);
        }

        $eloquent_results = $query->get();

        foreach($eloquent_results as $eloquent_result) {
            $results[] = self::eloquentToEntityBeer($eloquent_result);
        }

        return $results;
    }

    public function random(int $limit = 5): array
    {
        $results = [];

        $eloquent_beers = EloquentBeer::inRandomOrder()->take($limit)->get();
        foreach ($eloquent_beers as $eloquent_beer) {
            $results[] = $this->eloquentToEntityBeer($eloquent_beer);
        }

        return $results;
    }

    public function search(string $query): array
    {
        $results = [];
        foreach(EloquentBeer::search($query) as $eloquent_beer) {
            $results[] = $this->eloquentToEntityBeer($eloquent_beer);
        }
        return $results;
    }

    public function exists(string $name, Brewer $brewer): bool
    {
        $brewer = EloquentBrewer::find($brewer->id);
        if (!$brewer) return false;
        foreach ($brewer->beers()->get() as $beer) {
            if ($beer->name == $name) return true;
        }
        return false;
    }

    public function delete(Beer $beer): void
    {
        $eloquent_beer = EloquentBeer::find($beer->id);
        $eloquent_beer->delete();
    }

    private function eloquentToEntityBeer(EloquentBeer $eloquent_beer): Beer
    {
        $beer = new Beer();
        $beer->id = $eloquent_beer->id;
        $beer->name = $eloquent_beer->name;
        $beer->normalized_name = $eloquent_beer->normalized_name;
        $beer->style = new Style($eloquent_beer->type);
        $beer->created_at = $eloquent_beer->created_at;
        $beer->brewers = [];
        foreach($eloquent_beer->brewers()->get() as $brewer) {
            $beer->brewers[] = $this->brewer_repository->findById($brewer->id);
        }
        $beer->labels = [];
        foreach($eloquent_beer->labels()->get() as $label) {
            $beer->labels[] = $this->label_repository->findById($label->id);
        }

        return $beer;
    }

    private function entityToEloquentBeer(Beer $beer): EloquentBeer
    {
        $eloquent_beer = new EloquentBeer();
        if ($beer->id) {
            $eloquent_beer->id = $beer->id;
        }
        $eloquent_beer->name = $beer->name;
        $eloquent_beer->normalized_name = $beer->normalized_name;
        $eloquent_beer->type = $beer->style->name;
        $eloquent_beer->created_at = $beer->created_at;

        return $eloquent_beer;
    }

    private function populateEloquentBeer(EloquentBeer $eloquent_beer, Beer $beer): EloquentBeer
    {
        $eloquent_beer->id = $beer->id;
        $eloquent_beer->name = $beer->name;
        $eloquent_beer->normalized_name = $beer->normalized_name;
        $eloquent_beer->type = $beer->style->name;
        $eloquent_beer->created_at = $beer->created_at;

        return $eloquent_beer;
    }

    private function saveBrewer(EloquentBeer $beer, Brewer $brewer)
    {
        $current_brewer = $beer->brewers()->first();
        if (!$current_brewer || $current_brewer->id !== $brewer->id) {
            $beer->brewers()->sync([$brewer->id]);
        }
    }

    public function findByBrewerId($id): array
    {
        $results = [];
        foreach(EloquentBeer::whereHas('brewers', function($q) use($id){
            $q->where('id',$id);
        })->get()  as $eloquent_beer) {
            $results[] = $this->eloquentToEntityBeer($eloquent_beer);
        }
        return $results;
    }
}
