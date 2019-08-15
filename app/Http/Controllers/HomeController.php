<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        $stats = [
            [
                'name' => __('Beers'),
                'icon' => 'beer',
                'value' => Beer::count()
            ],
            [
                'name' => __('Brewers'),
                'icon' => 'industry',
                'value' => Brewer::count()
            ],
            [
                'name' => __('Countries'),
                'icon' => 'globe-europe',
                'value' => Brewer::distinct_countries()->count()
            ],
            [
                'name' => __('Styles'),
                'icon' => 'font',
                'value' => Beer::distinct_types()->count()
            ]
        ];
        return view('frontend.home',[
            'stats' => $stats
        ]);
    }

    public function find(Request $request)
    {
        $query = $request->get('query');

        $beers = Beer::search($query);

        return view('find',[
            'query' => $query,
            'beers' => $beers
        ]);
    }
}
