<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        return view('dashboard', [
            'beers' => Beer::count(),
            'brewers' => Brewer::count()
        ]);
    }

    public function upload_csv(Request $request)
    {
        $file = $request->file('csv');

        return "file is ".$file->getSize()." bytes widht";
    }
}
