<?php

namespace App\Http\Controllers;

use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Label\TagRepository;

class TagController extends Controller
{
    private $beer_repository;
    private $tag_repository;

    public function __construct(
        BeerRepository $beer_repository,
        TagRepository $tag_repository
    ) {
        $this->beer_repository = $beer_repository;
        $this->tag_repository = $tag_repository;
    }

    public function showTag(int $id)
    {
        $tag = $this->tag_repository->findById($id);
        $beers = $this->beer_repository->findByTagId($id);
        $beer_ids = array_map(function ($beer) {
            return $beer->id;
        }, $beers->toArray());
        return view('frontend.tag.tag', [
            'tag' => $tag,
            'beer_ids' => implode(',', $beer_ids),
        ]);
    }
}
