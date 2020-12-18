<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Common\Year;
use Larabeers\Domain\Location\FlagRepository;
use Larabeers\Services\CountBeersByYear;

class ApiController extends Controller
{
    private $beer_repository;
    private $flag_repository;
    private $count_beers_by_year_service;

    public function __construct(
        BeerRepository $beer_repository,
        FlagRepository $flag_repository,
        CountBeersByYear $count_beers_by_year_service
    ) {
        $this->beer_repository = $beer_repository;
        $this->flag_repository = $flag_repository;
        $this->count_beers_by_year_service = $count_beers_by_year_service;
    }

    public function randomBeers(Request $request) {
        $beers = [];
        foreach ($this->beer_repository->random(10) as $beer) {
            $beers[] = $this->buildBeerDataArray($beer);
        }
        return response()->json($beers);
    }

    public function searchBeers(Request $request) {
        $query = $request->get('query');

        $beers = [];
        foreach ($this->beer_repository->search($query) as $beer) {
            $beers[] = $this->buildBeerDataArray($beer);
        }

        return response()->json($beers);
    }

    public function findBeersById(Request $request)
    {
        $beer_ids = $request->get('beer_ids');

        $beers = [];
        foreach( explode(',', $beer_ids) as $beer_id) {
            $beer = $this->beer_repository->findById($beer_id);
            if (!$beer) continue;
            $beers[] = $this->buildBeerDataArray($beer);
        }

        return response()->json($beers);
    }

    public function countBeersByYear(Request $request)
    {
        $year_from = (int) $request->get('from');
        $year_to = (int) $request->get('to');

        if (!$year_from) {
            $year_from = $this->getOldestLabel();
        }
        if (!$year_to) {
            $year_to = (int) date_create()->format('Y');
        }

        $result = [];
        for ($year = $year_from; $year <= $year_to; $year++) {
            $result[$year] = $this->count_beers_by_year_service->execute(new Year($year));
        }

        return response()->json($result);
    }

    private function buildBeerDataArray(Beer $beer): array
    {
        $image = URL::asset('img/label-template.jpg');
        if (count($beer->labels) && $beer->labels[0]->sticker) {
            $sticker = $beer->labels[0]->sticker;
            $image = $sticker->thumbnail ?? $sticker->url;
        }

        $year = null;
        if (count($beer->labels)) {
            $year = $beer->labels[0]->year;
        }

        return [
            'id' => $beer->id,
            'name' => $beer->name,
            'brewer' => $beer->brewers[0]->name,
            'thumbnail' => $image,
            'flag' => $this->flag_repository->get($beer->brewers[0]->city->country->name),
            'year' => $year,
            'country' => $beer->brewers[0]->city->country->name
        ];
    }

    private function getOldestLabel()
    {
        return 2004;
    }
}
