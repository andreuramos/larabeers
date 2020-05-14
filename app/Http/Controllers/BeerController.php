<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Beer\Style;
use Larabeers\Domain\Label\Tag;
use Larabeers\External\BeerRepository;
use Larabeers\Services\CreateBeer;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Services\DeleteBeer;
use Larabeers\Services\UpdateBeer;
use Larabeers\Services\UpdateLabel;

class BeerController extends Controller
{
    private $private_methods = [
        'new_beer', 'create_beer', 'edit_beer', 'update_beer',
        'add_label_to_beer', 'update_label', 'delete_beer',
    ];

    private $beer_repository;
    private $create_beer;
    private $create_label_to_beer;
    private $delete_beer;
    private $update_beer;
    private $update_label;

    public function __construct(
        BeerRepository $beer_repository,
        CreateBeer $create_beer,
        CreateLabelToBeer $create_label_to_beer,
        DeleteBeer $delete_beer,
        UpdateBeer $update_beer,
        UpdateLabel $update_label
    ) {
        $this->beer_repository = $beer_repository;
        $this->create_beer = $create_beer;
        $this->create_label_to_beer = $create_label_to_beer;
        $this->delete_beer = $delete_beer;
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

    public function show_beer($id)
    {
        $beer = $this->beer_repository->findById($id);
        if (!$beer)
            abort(404);
        return view('frontend.beer.beer', ['beer' => $beer]);
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

        return redirect()->action('BeerController@edit_beer', ['id' => $id]);
    }

    public function add_label_to_beer(Request $request, $beer_id)
    {
        $image = $request->file('label');
        if (!$image) {
            $request->session()->flash('error', "New label must contain an image");
        }
        $file_route = public_path() . '/upload/';
        $image->move($file_route,$image->getClientOriginalName());
        $file_path = $file_route . '/' . $image->getClientOriginalName();

        $tag_names = $request->get('tag_names');
        $tags = [];
        if ($tag_names) {
            foreach (explode('|', $tag_names) as $tag_name) {
                $tags[] = new Tag($tag_name);
            }
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
        return redirect()->action('BeerController@edit_beer', ['id' => $beer_id]);
    }

    public function delete_beer(Request $request, int $beer_id)
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
