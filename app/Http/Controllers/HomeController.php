<?php

namespace App\Http\Controllers;

use App\Beer;
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
        return view('home');
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
