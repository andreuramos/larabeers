<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Brewer;
use App\Label;
use App\Tag;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Larabeers\Entities\BeerCriteria;
use Larabeers\External\BeerRepository;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Services\UpdateBeer;
use Larabeers\Services\UpdateLabel;
use Larabeers\Utils\NormalizeString;

class DashboardController extends Controller
{
    private $beer_repository;
    private $beer_updater;
    private $label_creator;
    private $update_label;

    public function __construct(
        BeerRepository $beer_repository,
        UpdateBeer $beer_updater,
        CreateLabelToBeer $label_creator,
        UpdateLabel $update_label
    ) {
        $this->beer_repository = $beer_repository;
        $this->beer_updater = $beer_updater;
        $this->label_creator = $label_creator;
        $this->update_label = $update_label;
    }

    public function callAction($method, $parameters)
    {
        if (!Auth::check())
            return redirect('login');
        return parent::callAction($method, $parameters);
    }

    public function index()
    {
        $criteria = new BeerCriteria();
        $criteria->addOrder('created_at');
        $criteria->addLimit(10);
        $last_beers = $this->beer_repository->findByCriteria($criteria);

        return view('dashboard', [
            'beers' => Beer::count(),
            'brewers' => Brewer::count(),
            'last_beers' => $last_beers
        ]);
    }

    public function upload_csv(Request $request)
    {
        $file = $request->file('csv');
        $file->move(public_path() . '/upload', 'import.csv');

        $fd = fopen(public_path() . '/upload/import.csv', 'r');
        $row = fgetcsv($fd);
        $new_beers = 0;

        while (($row = fgetcsv($fd)) !== FALSE) {
            $csv_brewer = $row[0];
            $csv_beer = $row[1];
            if (Beer::where('name', $csv_beer)->first() || !$csv_beer)
                continue;
            echo "Importing " . $row[1] . "<br>";
            $new_beers++;
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
                'normalized_name' => NormalizeString::execute($csv_beer),
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

        $request->session()->flash('success', "$new_beers beers added successfully");
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
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            abort(404);
        }
        $brewers = $this->getBrewers($beer);

        return view('dashboard.beer.form', ['beer' => $beer, 'brewers' => $brewers]);
    }

    private function getBrewers(\Larabeers\Entities\Beer $beer): array
    {
        $brewers = [];

        foreach($beer->brewers as $brewer) {
            $brewers[$brewer->id] = $brewer->name;
        }

        return $brewers;
    }

    public function update_beer(Request $request, $id)
    {
        $name = $request->get('name');
        $brewer_id = $request->get('brewer_id');

        try {
            $this->beer_updater->execute($id, $name, $brewer_id);
            $request->session()->flash('success', "Beer updated successfully");
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->action('DashboardController@edit_beer', ['id' => $id]);
    }

    public function add_label_to_beer(Request $request, $beer_id)
    {
        $image = $request->file('label');
        if ($image) {
            $label_id = $this->label_creator->execute($beer_id, $image->getRealPath(), [
                'year' => $request->get('year'),
                'album' => $request->get('album'),
                'page' => $request->get('page'),
                'position' => $request->get('position'),
            ]);
        }

        $request->session()->flash('success', "Label added successfully");
        return redirect()->action('DashboardController@edit_beer', ['id' => $beer_id]);
    }

    public function update_label(Request $request, $id)
    {
        $label_id = $request->get('label_id');
        $beer_id = $request->get('beer_id');
        $metadata = [
            'year' => $request->get('year'),
            'album' => $request->get('album'),
            'page' => $request->get('page'),
            'position' => $request->get('position'),
        ];

        if ($request->has('label')) {
            $image = $request->file('label')->getRealPath();
        } else {
            $image = null;
        }

        $this->update_label->execute($label_id, $image, $metadata);

        $request->session()->flash('success', "Label updated successfully");
        return redirect()->action('DashboardController@edit_beer', ['id' => $beer_id]);
    }

    public function settings(Request $request)
    {

        $account_connected = false;
        $refresh_token = Auth::user()->google_refresh_token; //$_COOKIE['google_refresh_token'];

        if ($refresh_token !== null) {
            $client = $this->getGoogleClient();
            $auth_url = $client->createAuthUrl();
            $access = $client->fetchAccessTokenWithRefreshToken(Crypt::decrypt($refresh_token));

            if (!array_key_exists('error', $access)) {
                $account_connected = true;
            }
        }

        return view('dashboard.settings', [
            'auth_url' => $auth_url,
            'account_connected' => $account_connected
        ]);
    }

    public function google_auth_comeback(Request $request)
    {
        $code = $request->get('code');

        // https://developers.google.com/drive/api/v3/quickstart/php
        $client = new \Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setRedirectUri(url('/dashboard/settings/google_auth_comeback'));
        $client->setAccessType('offline');
        $access = $client->fetchAccessTokenWithAuthCode($code);

        //handle errors
        if (array_key_exists('error', $access)) {
            return redirect()
                ->action('DashboardController@settings')
                ->with('error', $access['error_description']);
        }
        $refresh_token = $access['refresh_token'];

        $user = Auth::user();
        $user->google_refresh_token = Crypt::encrypt($refresh_token);
        $user->save();

        return redirect()
            ->action('DashboardController@settings')
            ->with('success', "Google Account correctly linked");
    }

    /**
     * @return \Google_Client
     */
    private function getGoogleClient(): \Google_Client
    {
        $client = new \Google_Client();
        $client->setApplicationName('Larabeers');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setClientId(env('GOOGLE_API_APP_ID'));
        $client->setClientSecret(env('GOOGLE_API_SECRET'));
        $client->setAccessType('offline');
        $client->setRedirectUri(url('/dashboard/settings/google_auth_comeback'));
        return $client;
    }
}
