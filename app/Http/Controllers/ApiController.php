<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Larabeers\External\BeerRepository;

class ApiController extends Controller
{
    private $beer_repository;

    public function __construct(
        BeerRepository $beer_repository
    ) {
        $this->beer_repository = $beer_repository;
    }

    public function randomBeers(Request $request) {
        $beers = [];
        foreach ($this->beer_repository->random(10) as $beer) {
            $image = URL::asset('img/label-template.jpg');
            if (count($beer->labels) && $beer->labels[0]->sticker) {
                $sticker = $beer->labels[0]->sticker;
                $image = $sticker->thumbnail ?? $sticker->url;
            }

            $beers[] = [
                'id' => $beer->id,
                'name' => $beer->name,
                'brewer' => $beer->brewers[0]->name,
                'thumbnail' => $image
            ];
        }
        return response()->json($beers);
    }
}
