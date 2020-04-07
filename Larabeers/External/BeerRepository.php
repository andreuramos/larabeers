<?php

namespace Larabeers\External;

use App\Beer as EloquentBeer;
use Larabeers\Entities\Beer;
use Larabeers\Entities\BeerCriteria;
use Larabeers\Entities\Style;

class BeerRepository
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
        return $this->eloquentToEntityBeer($beer);
    }

    public function save(Beer $beer): void
    {
        if ($beer->id) {
            $eloquent_beer = EloquentBeer::find($beer->id);
            $eloquent_beer = $this->populateEloquentBeer($eloquent_beer, $beer);
        } else {
            $eloquent_beer = $this->entityToEloquentBeer($beer);
        }

        $eloquent_beer->save();
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

    private function eloquentToEntityBeer(EloquentBeer $eloquent_beer): Beer
    {
        $beer = new Beer();
        $beer->id = $eloquent_beer->id;
        $beer->name = $eloquent_beer->name;
        $beer->normalized_name = $eloquent_beer->normalized_name;
        $beer->style = new Style($eloquent_beer->type);
        $beer->created_at = $eloquent_beer->created_at;
        foreach($eloquent_beer->brewers()->get() as $brewer) {
            $beer->brewers[] = $this->brewer_repository->findById($brewer->id);
        }
        foreach($eloquent_beer->labels()->get() as $label) {
            $beer->labels[] = $this->label_repository->findById($label->id);
        }

        return $beer;
    }

    private function entityToEloquentBeer(Beer $beer): EloquentBeer
    {
        $eloquent_beer = new EloquentBeer();
        $eloquent_beer->id = $beer->id;
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
}
