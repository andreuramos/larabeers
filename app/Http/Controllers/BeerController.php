<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Beer\Style;
use Larabeers\Domain\Label\Tag;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\External\EloquentLabelRepository;
use Larabeers\Services\CreateBeer;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Services\DeleteBeer;
use Larabeers\Services\DeleteLabel;
use Larabeers\Services\UpdateBeer;
use Larabeers\Services\UpdateLabel;

class BeerController extends Controller
{
    private $private_methods = [
        'newBeer', 'createBeer', 'editBeer', 'updateBeer',
        'addLabelToBeer', 'updateLabel', 'deleteBeer', 'deleteLabelFromBeer',
    ];

    private $beer_repository;
    private $label_repository;
    private $create_beer;
    private $create_label_to_beer;
    private $delete_beer;
    private $delete_label;
    private $update_beer;
    private $update_label;

    public function __construct(
        EloquentBeerRepository $beer_repository,
        EloquentLabelRepository $label_repository,
        CreateBeer $create_beer,
        CreateLabelToBeer $create_label_to_beer,
        DeleteBeer $delete_beer,
        DeleteLabel $delete_label,
        UpdateBeer $update_beer,
        UpdateLabel $update_label
    ) {
        $this->beer_repository = $beer_repository;
        $this->label_repository = $label_repository;

        $this->create_beer = $create_beer;
        $this->create_label_to_beer = $create_label_to_beer;
        $this->delete_beer = $delete_beer;
        $this->delete_label = $delete_label;
        $this->update_beer = $update_beer;
        $this->update_label = $update_label;
    }

    public function callAction($method, $parameters)
    {
        if (in_array($method, $this->private_methods) && !Auth::check()) {
            return redirect('login');
        }
        return parent::callAction($method, $parameters);
    }

    public function showBeer($id)
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            abort(404);
        }
        return view('frontend.beer.beer', ['beer' => $beer]);
    }

    public function newBeer()
    {
        $beer = new Beer();
        $beer->brewers[] = new Brewer();
        $beer->style = new Style("");
        $beer->labels = [];
        return view('dashboard.beer.form', ['beer' => $beer]);
    }

    public function createBeer(Request $request)
    {
        $name = $request->get('name');
        $brewer_id = $request->get('autocomplete_brewer_id');
        $style_name = $request->get('beer_style');

        try {
            $style = new Style($style_name);
            $id = $this->create_beer->execute($name, $brewer_id, $style);
            $request->session()->flash('success', "Beer created successfully");
            return redirect()->action('BeerController@edit_beer', ['id' => $id]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function editBeer($id)
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer) {
            abort(404);
        }

        return view('dashboard.beer.form', ['beer' => $beer]);
    }

    public function updateBeer(Request $request, $id)
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

        return redirect()->action('BeerController@edit_beer', ['id' => $id]);
    }

    public function addLabelToBeer(Request $request, $beer_id)
    {
        $image = $request->file('label');
        if (!$image) {
            $request->session()->flash('error', "New label must contain an image");
            return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
        }
        $file_route = public_path() . '/upload/';
        $image->move($file_route, $image->getClientOriginalName());
        $file_path = $file_route . '/' . $image->getClientOriginalName();

        $tag_names = $request->get('tag_names');

        $tags = [];
        if ($tag_names) {
            foreach (explode('|', $tag_names) as $tag_name) {
                $tags[] = new Tag($tag_name);
            }
        }

        if (!$this->validateLabelForm($request)) {
            return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
        }

        try {
            $this->create_label_to_beer->execute($beer_id, $file_path, [
                'year' => $request->get('year'),
                'album' => $request->get('album'),
                'page' => $request->get('page'),
                'position' => $request->get('position'),
            ], $tags);
            $request->session()->flash('success', "Label added successfully");
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
    }

    public function updateLabel(Request $request, $id)
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
        return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
    }

    public function deleteLabelFromBeer(Request $request, $beer_id, $label_id)
    {
        $label = $this->label_repository->findById($label_id);
        $this->delete_label->execute($label);

        $request->session()->flash('success', "Label deleted successfylly");
        return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
    }

    private function validateLabelForm(Request $request)
    {
        $errors_found = false;
        if (!$request->get('year')) {
            $errors_found = true;
            $request->session()->flash('error', "Invalid Year");
        }

        if (!$request->get('album')) {
            $errors_found = true;
            $request->session()->flash('error', "Invalid Album");
        }

        if (!$request->get('page')) {
            $errors_found = true;
            $request->session()->flash('error', "Invalid Album");
        }

        if (!$request->get('position')) {
            $errors_found = true;
            $request->session()->flash('error', "Invalid Position");
        }

        return !$errors_found;
    }

    public function deleteBeer(Request $request, int $beer_id)
    {
        $beer = $this->beer_repository->findById($beer_id);
        try {
            $this->delete_beer->execute($beer);
            $request->session()->flash('success', "Beer deleted successfully");
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
        }

        return redirect()->action('DashboardController@index');
    }
}
