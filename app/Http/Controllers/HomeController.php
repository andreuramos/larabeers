<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use App\Label;
use Illuminate\Http\Request;
use Larabeers\Domain\Location\CountryMapper;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\Services\SearchBrewer;
use Larabeers\Services\SearchStyle;
use Larabeers\Services\SearchTag;

class HomeController extends Controller
{
    private $beer_repository;
    private $search_brewer;
    private $search_style;
    private $search_tag;
    private $country_mapper;

    public function __construct(
        EloquentBeerRepository $beer_repository,
        SearchBrewer $search_brewer,
        SearchStyle $search_style,
        SearchTag $search_tag,
        CountryMapper $country_mapper
    ) {
        $this->beer_repository = $beer_repository;
        $this->search_brewer = $search_brewer;
        $this->search_style = $search_style;
        $this->search_tag = $search_tag;
        $this->country_mapper = $country_mapper;
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
                'value' => Brewer::distinctCountries()->count(),
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

    public function ajaxSearch(Request $request)
    {
        $beers = $this->beer_repository->search($request->get('query'));
        return view("frontend.beer_list", ["beers" => $beers]);
    }

    public function ajaxBrewerAutocomplete(Request $request)
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

    public function ajaxStyleAutocomplete(Request $request)
    {
        $query = $request->get('query');
        $results = [];

        foreach ($this->search_style->execute($query) as $style) {
            $results[] = $style->name;
        }

        return response()->json($results);
    }

    public function ajaxTagAutocomplete(Request $request)
    {
        $query = $request->get('query');
        $results = [];

        foreach ($this->search_tag->execute($query) as $tag) {
            $results[] = $tag->text;
        }

        return response()->json($results);
    }

    private static function sortCountries($a, $b)
    {
        if ($a['beers'] == $b['beers']) {
            return 0;
        }
        return $a['beers'] > $b['beers'] ? -1 : 1;
    }

    public function listCountries()
    {
        $countries = [];
        foreach (Brewer::distinctCountries() as $db_country) {
            $country_beers = 0;
            $country_name = $db_country->country;
            foreach (Brewer::where('country', $country_name)->get() as $country_brewer) {
                $country_beers += $country_brewer->beers->count();
            }
            $country_name = $this->getCountryName($country_name);
            $countries[] = [
                'name' => $country_name,
                'beers' => $country_beers
            ];
        }
        usort($countries, [self::class, "sortCountries"]);
        return view('frontend.stats.countries', ['countries' => $countries]);
    }

    public function listYears()
    {
        return view('frontend.stats.years');
    }

    private function getCountryName(string $country_name): string
    {
        return $this->country_mapper->execute($country_name);
    }
}
