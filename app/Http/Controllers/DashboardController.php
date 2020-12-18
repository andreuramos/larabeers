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
use Illuminate\Support\Facades\DB;
use Larabeers\Domain\Beer\BeerCriteria;
use Larabeers\Domain\Common\Year;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\Services\CountBeersByYear;
use Larabeers\Services\CreateBrewer;
use Larabeers\Utils\NormalizeString;

class DashboardController extends Controller
{
    private $beer_repository;
    private $create_brewer;
    private $count_beers_by_year;

    public function __construct(
        EloquentBeerRepository $beer_repository,
        CreateBrewer $create_brewer,
        CountBeersByYear $count_beers_by_year
    ) {
        $this->beer_repository = $beer_repository;
        $this->create_brewer = $create_brewer;
        $this->count_beers_by_year = $count_beers_by_year;
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
        $last_beer_ids = array_map(function($beer) {return $beer->id;}, $last_beers->toArray());
        $beers_with_picture = $this->countBeersWithPicture();
        $this_year_beers = $this->getYearBeers(date_create()->format('Y'));
        $last_year_beers = $this->getYearBeers(date_create()->sub(new \DateInterval("P1Y"))->format('Y'));

        $previous_year_percent = round($this_year_beers / $last_year_beers * 100,2);

        $beers_count = EloquentBeer::count();
        return view('dashboard', [
            'beers' => $beers_count,
            'beers_with_picture' => $beers_with_picture,
            'beers_with_picture_percent' => round(($beers_with_picture / $beers_count ) * 100, 2),
            'brewers' => EloquentBrewer::count(),
            'this_year_beers' => $this_year_beers,
            'previous_year_percent' => $previous_year_percent,
            'last_beer_ids' => implode(',',$last_beer_ids),
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

        $error = $this->get_google_comeback_errors($access);
        if ($error) {
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

    /**
     * @param array $access
     * @return mixed|string|null
     */
    private function get_google_comeback_errors(array $access)
    {
        $error = null;
        if (array_key_exists('error', $access)) {
            $error = $access['error_description'];
        }
        if (!array_key_exists('refresh_token', $access)) {
            $error = "Refresh token not found. Try removing permissions on your account to this app";
        }
        return $error;
    }

    private function countBeersWithPicture()
    {
        $sql = 'SELECT count(distinct(beer_id)) FROM stickers JOIN labels ON stickers.label_id = labels.id;';
        $res = DB::select($sql);
        return (int) $res[0]->count;
    }

    private function getYearBeers($year)
    {
        $year_obj = new Year($year);

        return $this->count_beers_by_year->execute($year_obj);
    }
}
