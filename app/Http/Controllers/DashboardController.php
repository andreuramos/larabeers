<?php

namespace App\Http\Controllers;

use App\Beer as EloquentBeer;
use App\Brewer as EloquentBrewer;
use App\Label;
use App\Tag as EloquentTag;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Larabeers\Entities\Beer;
use Larabeers\Entities\BeerCriteria;
use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Entities\Country;
use Larabeers\Entities\Style;
use Larabeers\Entities\Tag;
use Larabeers\External\BeerRepository;
use Larabeers\Services\CreateBeer;
use Larabeers\Services\CreateBrewer;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Services\UpdateBeer;
use Larabeers\Services\UpdateLabel;
use Larabeers\Utils\NormalizeString;

class DashboardController extends Controller
{
    private $beer_repository;
    private $create_beer;
    private $create_brewer;
    private $create_label_to_beer;
    private $update_beer;
    private $update_label;

    public function __construct(
        BeerRepository $beer_repository,
        CreateBeer $create_beer,
        CreateBrewer $create_brewer,
        CreateLabelToBeer $create_label_to_beer,
        UpdateBeer $update_beer,
        UpdateLabel $update_label
    ) {
        $this->beer_repository = $beer_repository;
        $this->create_beer = $create_beer;
        $this->create_brewer = $create_brewer;
        $this->create_label_to_beer = $create_label_to_beer;
        $this->update_beer = $update_beer;
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
            'beers' => EloquentBeer::count(),
            'brewers' => EloquentBrewer::count(),
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
            if (EloquentBeer::where('name', $csv_beer)->first() || !$csv_beer)
                continue;
            echo "Importing " . $row[1] . "<br>";
            $new_beers++;
            $brewer = EloquentBrewer::where('name', $csv_brewer)->first();
            if (!$brewer) {
                $brewer = EloquentBrewer::create([
                    'name' => $csv_brewer,
                    'country' => $row[7],
                    'city' => $row[8]
                ]);
            }

            $beer = EloquentBeer::create([
                'name' => $csv_beer,
                'normalized_name' => NormalizeString::execute($csv_beer),
                'type' => $row[2]
            ]);

            $beer->brewers()->save($brewer);

            $label = $this->store_label(
                $beer->id, $row[9], $row[10], $row[4], $row[5], $row[6], $row[14]
            );

            $tag = EloquentTag::where('text', $row[13])->first();
            if (!$tag) {
                $tag = EloquentTag::create([
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

    public function new_beer()
    {
        $beer = new Beer();
        $beer->brewers[] = new Brewer();
        $beer->style = new Style("");
        $beer->labels = [];
        return view('dashboard.beer.form', ['beer' => $beer]);
    }

    public function create_beer(Request $request)
    {
        $name = $request->get('name');
        $brewer_id = $request->get('autocomplete_brewer_id');
        $style_name = $request->get('beer_style');

        //try {
            $style = new Style($style_name);
            $id = $this->create_beer->execute($name, $brewer_id, $style);
            $request->session()->flash('success', "Beer created successfully");
            return redirect()->action('DashboardController@edit_beer', ['id' => $id]);
        /*} catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }*/
    }

    public function edit_beer($id)
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            abort(404);
        }

        return view('dashboard.beer.form', ['beer' => $beer]);
    }

    public function update_beer(Request $request, $id)
    {
        $name = $request->get('name');
        $brewer_id = $request->get('autocomplete_brewer_id');
        $style_name = $request->get('beer_style');

        try {
            $style = new Style($style_name);
            $this->update_beer->execute($id, $name, $brewer_id, $style);
            $request->session()->flash('success', "Beer updated successfully");
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->action('DashboardController@edit_beer', ['id' => $id]);
    }

    public function add_label_to_beer(Request $request, $beer_id)
    {
        $image = $request->file('label');
        if (!$image) {
            $request->session()->flash('error', "New label must contain an image");
        }

        $tag_names = $request->get('tag_names');
        $tags = [];
        if ($tag_names) {
            foreach (explode('|', $tag_names) as $tag_name) {
                $tags[] = new Tag($tag_name);
            }
        }
        try {
            $this->create_label_to_beer->execute($beer_id, $image->getRealPath(), [
                'year' => $request->get('year'),
                'album' => $request->get('album'),
                'page' => $request->get('page'),
                'position' => $request->get('position'),
            ], $tags);
            $request->session()->flash('success', "Label added successfully");
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
        }

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

        $tag_names = $request->get('tag_names');
        $tags = [];
        if ($tag_names) {
            foreach (explode('|', $tag_names) as $tag_name) {
                $tags[] = new Tag($tag_name);
            }
        }

        if ($request->has('label')) {
            $image = $request->file('label')->getRealPath();
        } else {
            $image = null;
        }

        $this->update_label->execute($label_id, $image, $metadata, $tags);

        $request->session()->flash('success', "Label updated successfully");
        return redirect()->action('DashboardController@edit_beer', ['id' => $beer_id]);
    }

    public function settings(Request $request)
    {
        $account_connected = false;
        $refresh_token = Auth::user()->google_refresh_token;
        $client = $this->getGoogleClient();
        $auth_url = $client->createAuthUrl();

        if ($refresh_token !== null) {
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
        $client->setApprovalPrompt('force');
        $access = $client->fetchAccessTokenWithAuthCode($code);

        //handle errors
        if (array_key_exists('error', $access) || !array_key_exists('refresh_token', $access)) {
            $error = array_key_exists('error_description', $access) ?
                $access['error_description'] :
                print_r($access,1);
            return redirect()
                ->action('DashboardController@settings')
                ->with('error', $error);
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

    public function create_brewer(Request $request)
    {

    }

    public function ajax_create_brewer(Request $request)
    {
        $name = (string) $request->post('name');
        $city_name = (string) $request->post('city');
        $country_name = (string) $request->post('country');

        try {
            // @TODO: Build city through factory
            $country = new Country($country_name);
            $city = new City($city_name, $country);
            $id = $this->create_brewer->execute($name, $city);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'id' => $id,
            'name' => $name
        ]);
    }

    public function update_brewer(Request $request, int $id)
    {

    }
}
