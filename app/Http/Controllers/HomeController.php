<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use App\Label;
use Illuminate\Http\Request;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\External\EloquentBrewerRepository;
use Larabeers\Services\SearchBrewer;
use Larabeers\Services\SearchStyle;
use Larabeers\Services\SearchTag;

class HomeController extends Controller
{
    private $beer_repository;
    private $brewer_repository;
    private $search_brewer;
    private $search_style;
    private $search_tag;

    public function __construct(
        EloquentBeerRepository $beer_repository,
        EloquentBrewerRepository $brewer_repository,
        SearchBrewer $search_brewer,
        SearchStyle $search_style,
        SearchTag $search_tag
    ) {
        $this->beer_repository = $beer_repository;
        $this->brewer_repository = $brewer_repository;
        $this->search_brewer = $search_brewer;
        $this->search_style = $search_style;
        $this->search_tag = $search_tag;
    }

    public function home()
    {
        $stats = [
            [
                'name' => __('Beers'),
                'icon' => 'beer',
                'value' => Beer::count(),
                'url' => null,
            ],
            [
                'name' => __('Brewers'),
                'icon' => 'industry',
                'value' => Brewer::count(),
                'url' => null,
            ],
            [
                'name' => __('Countries'),
                'icon' => 'globe-europe',
                'value' => Brewer::distinct_countries()->count(),
                'url' => url('stats/countries'),
            ],
            [
                'name' => __('Stickers'),
                'icon' => 'sticky-note',
                'value' => Label::count(),
                'url' => null,
            ]
        ];
        return view('frontend.home', [
            'stats' => $stats,
            'beers' => $this->beer_repository->random(5)
        ]);
    }

    public function ajax_search(Request $request)
    {
        $beers = $this->beer_repository->search($request->get('query'));
        return view("frontend.beer_list", ["beers" => $beers]);
    }

    public function ajax_brewer_autocomplete(Request $request)
    {
        $query = $request->get('query');
        $results = [];

        $brewers = $this->search_brewer->execute($query);
        foreach ($brewers as $brewer) {
            $results[] = [
                'id' => $brewer->id,
                'name' => $brewer->name
            ];
        }

        return response()->json($results);
    }

    public function ajax_style_autocomplete(Request $request)
    {
        $query = $request->get('query');
        $results = [];

        foreach($this->search_style->execute($query) as $style) {
            $results[] = $style->name;
        }

        return response()->json($results);
    }

    public function ajax_tag_autocomplete(Request $request)
    {
        $query = $request->get('query');
        $results = [];

        foreach ($this->search_tag->execute($query) as $tag) {
            $results[] = $tag->text;
        }

        return response()->json($results);
    }

    private static function sort_countries($a, $b) {
        if ($a['beers'] == $b['beers']) return 0;
        return $a['beers'] > $b['beers'] ? -1 : 1;
    }

    public function list_countries()
    {
        $countries = [];
        foreach (Brewer::distinct_countries() as $db_country) {
            $country_beers = 0;
            $country_name = $db_country->country;
            foreach (Brewer::where('country',$country_name)->get() as $country_brewer) {
                $country_beers += $country_brewer->beers->count();
            }
            if ($country_name == "USA") $country_name = "United States";
            elseif ($country_name == "England") $country_name = "United Kingdom";
            elseif ($country_name == "Slovak Republic") $country_name = "Slovakia";
            $countries[] = [
                'name' => $country_name,
                'beers' => $country_beers
            ];
        }
        usort($countries, [self::class,"sort_countries"]);
        return view('frontend.stats.countries', ['countries' => $countries]);
    }

    public function list_years()
    {
        return view('frontend.stats.years');
    }

}
