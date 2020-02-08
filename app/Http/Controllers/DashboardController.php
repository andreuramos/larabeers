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

    private $beer_updater;

    public function __construct(UpdateBeer $beer_updater)
    {
        $this->beer_updater = $beer_updater;
    }

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
        $name = $request->get('name');
        $image = $request->file('label');

        $this->beer_updater->execute($id, $name);

        if ($image) {
            $label_id = $this->label_creator->execute($id, $image->getRealPath(), []);
            $this->setLabelMetadata->execute($label_id, [
                'year',
                'album',
                'page',
                'position'
            ]);
        }

        return redirect()->action('DashboardController@edit_beer', ['id' => $id]);
    }

    public function settings()
    {
        //TODO: only ask if no refresh token is set in cookies
        $client = new \Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setAccessType('offline');
        $client->setRedirectUri(url('/dashboard/settings/google_auth_comeback'));
        $auth_url = $client->createAuthUrl();

        return view('dashboard.settings', ['auth_url' => $auth_url]);
    }

    public function google_auth_comeback(Request $request)
    {
        $code = $request->get('code');

        // TODO: set a flash to tell the user the operation result
        // https://developers.google.com/drive/api/v3/quickstart/php
        $client = new \Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setRedirectUri(url('/dashboard/settings/google_auth_comeback'));
        $client->setAccessType('offline');
        $access = $client->fetchAccessTokenWithAuthCode($code);

        $refresh_token = $access['refresh_token'];

        return redirect()
            ->action('DashboardController@settings')
            ->withCookie('google_refresh_token', $refresh_token);
    }
}
