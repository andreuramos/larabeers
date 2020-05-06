<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Larabeers\External\BeerRepository;
use Larabeers\External\FlagRepository;

class ApiController extends Controller
{
    private $beer_repository;
    private $flag_repository;

    public function __construct(
        BeerRepository $beer_repository,
        FlagRepository $flag_repository
    ) {
        $this->beer_repository = $beer_repository;
        $this->flag_repository = $flag_repository;
    }

    public function randomBeers(Request $request) {
        $beers = [];
        foreach ($this->beer_repository->random(10) as $beer) {
            $image = URL::asset('img/label-template.jpg');
            if (count($beer->labels) && $beer->labels[0]->sticker) {
                $sticker = $beer->labels[0]->sticker;
                $image = $sticker->thumbnail ?? $sticker->url;
            }

            $year = null;
            if (count($beer->labels)) {
                $year = $beer->labels[0]->year;
            }

            $beers[] = [
                'id' => $beer->id,
                'name' => $beer->name,
                'brewer' => $beer->brewers[0]->name,
                'thumbnail' => $image,
                'flag' => $this->flag_repository->get($beer->brewers[0]->city->country->name),
                'year' => $year
            ];
        }
        return response()->json($beers);
    }
}
