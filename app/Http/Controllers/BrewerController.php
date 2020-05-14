<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Location\City;
use Larabeers\Domain\Location\Country;
use Larabeers\External\BeerRepository;
use Larabeers\External\BrewerRepository;
use Larabeers\Services\CreateBrewer;

class BrewerController extends Controller
{
    private $private_methods = [
        'new_brewer', 'create_brewer', 'ajax_create_brewer', 'update_brewer'
    ];

    private $beer_repository;
    private $brewer_repository;
    private $create_brewer;
    private $update_brewer;

    public function __construct(
        BeerRepository $beer_repository,
        BrewerRepository $brewer_repository,
        CreateBrewer $create_brewer
    ) {
        $this->beer_repository = $beer_repository;
        $this->brewer_repository = $brewer_repository;
        $this->create_brewer = $create_brewer;
    }

    public function callAction($method, $parameters)
    {
        if (in_array($method, $this->private_methods) && !Auth::check()) {
            return redirect('login');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    public function show_brewer($id)
    {
        $brewer = $this->brewer_repository->findById($id);
        if (!$brewer)
            abort(404);
        $beers = $this->beer_repository->findByBrewerId($id);
        return view('frontend.brewer.brewer', ['brewer' => $brewer, 'beers' => $beers]);
    }

    public function new_brewer()
    {
        return view('dashboard.brewer.form', ['brewer' => new Brewer()]);
    }

    public function create_brewer(Request $request)
    {
        list($name, $city_name, $country_name, $data) = $this->get_form_params($request);

        try {
            $country = new Country($country_name);
            $city = new City($city_name, $country);
            $brewer_id = $this->create_brewer->execute($name, $city, $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('brewer/'.$brewer_id);
    }

    public function edit_brewer($id)
    {
        $brewer = $this->brewer_repository->findById($id);
        return view('dashboard.brewer.form', ['brewer' => $brewer]);
    }

    public function update_brewer(Request $request, int $id)
    {
        list($name, $city_name, $country_name, $data) = $this->get_form_params($request);

        try {
            $country = new Country($country_name);
            $city = new City($city_name, $country);
            $brewer_id = $this->update_brewer->execute($id, $name, $city, $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        session()->flash('success', "Brewer updated correctly");
        return redirect('brewer/'.$brewer_id);    }

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

    /**
     * @param Request $request
     * @return array
     */
    private function get_form_params(Request $request): array
    {
        $name = $request->get('brewer_name');
        $city_name = $request->get('brewer_city');
        $country_name = $request->get('brewer_country');

        $logo = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->getRealPath();
        }

        $data = [
            'address' => $request->get('address'),
            'lat' => $request->get('lat'),
            'lng' => $request->get('lng'),
            'website' => $request->get('website'),
            'logo' => $logo
        ];
        return array($name, $city_name, $country_name, $data);
    }
}
