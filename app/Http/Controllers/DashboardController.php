<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use App\Helpers\StringHelper;
use App\Label;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Larabeers\External\BeerRepository;
use Larabeers\Services\UpdateBeer;
use Larabeers\Utils\NormalizeString;

class DashboardController extends Controller
{

    public function callAction($method, $parameters)
    {
        if (!Auth::check())
            return redirect('login');
        return parent::callAction($method, $parameters);
    }

    public function index()
    {
        return view('dashboard', [
            'beers' => Beer::count(),
            'brewers' => Brewer::count()
        ]);
    }

    public function upload_csv(Request $request)
    {
        $file = $request->file('csv');
        $file->move(public_path() . '/upload', 'import.csv');

        $fd = fopen(public_path() . '/upload/import.csv', 'r');
        $row = fgetcsv($fd);

        while (($row = fgetcsv($fd)) !== FALSE) {
            $csv_brewer = $row[0];
            $csv_beer = $row[1];
            if (Beer::where('name', $csv_beer)->first() || !$csv_beer)
                continue;
            echo "Importing " . $row[1] . "<br>";

            $brewer = Brewer::where('name', $csv_brewer)->first();
            if (!$brewer) {
                $brewer = Brewer::create([
                    'name' => $csv_brewer,
                    'country' => $row[7],
                    'city' => $row[8]
                ]);
            }

            $beer = Beer::create([
                'name' => $csv_beer,
                'normalized_name' => StringHelper::normalize($csv_beer),
                'type' => $row[2]
            ]);

            $beer->brewers()->save($brewer);

            $label = $this->store_label(
                $beer->id, $row[9], $row[10], $row[4], $row[5], $row[6], $row[14]
            );

            $tag = Tag::where('text', $row[13])->first();
            if (!$tag) {
                $tag = Tag::create([
                    'text' => $row[13]
                ]);
            }

            $label->tags()->save($tag);
        }
        fclose($fd);
        unlink(public_path() . '/upload/import.csv');
        return redirect('/dashboard');
    }

    private function store_label($beer_id, $year, $month, $album, $page, $position, $other_year)
    {
        $label_data = [
            'beer_id' => $beer_id,
            'album' => $album,
            'page' => $page,
            'position' => $position
        ];

        if ($year) $label_data['year'] = $year;
        if ($month) $label_data['month'] = $month;

        $label = Label::create($label_data);

        if ($other_year) {
            unset($label_data['month']);
            $label_data['year'] = $other_year;
            Label::create($label_data);
        }
        return $label;
    }

    public function edit_beer($id)
    {
        $beer = Beer::find($id);
        if (!$beer) {
            abort(404);
        }
        return view('dashboard.beer.form', ['beer' => $beer]);
    }

    public function update_beer(Request $request, $id)
    {
        $update_beer_service = new UpdateBeer(new BeerRepository(), new NormalizeString());

        $name = $request->get('name');

        $update_beer_service->execute($id, $name);

        return redirect()->action('DashboardController@edit_beer', ['id' => $id]);
    }
}
