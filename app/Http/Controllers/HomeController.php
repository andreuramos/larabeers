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
            'stats' => $stats,
            'beers' => Beer::random(5)
        ]);
    }

    public function ajax_search(Request $request)
    {
        $beers = Beer::search($request->get('query'));
        return view("frontend.beer_list",["beers"=>$beers]);
    }

    public function show_beer($id)
    {
        $beer = Beer::find($id);
        if (!$beer)
            abort(404);
        return view('frontend.beer.beer',['beer'=>$beer]);
    }
}
