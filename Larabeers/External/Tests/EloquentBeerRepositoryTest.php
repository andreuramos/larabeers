<?php

namespace Larabeers\External\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\Style;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Brewer\BrewerRepository;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\External\EloquentBeerRepository;
use Tests\TestCase;

class EloquentBeerRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    private $brewer_repository;
    private $label_repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->brewer_repository = $this->prophesize(BrewerRepository::class);
        $this->label_repository = $this->prophesize(LabelRepository::class);
    }

    public function tearDown(): void
    {
        $this->prophesize()->checkProphecyMethodsPredictions();
    }

    public function test_saves_beer()
    {
        $before_beer_count = DB::table('beers')->count();

        $brewer = new Brewer();
        $brewer->name = "Douvel brewery";

        $beer = new Beer();
        $beer->name = "douvel";
        $beer->brewers = [$brewer];
        $beer->style = new Style("trappist");

        $this->repository()->save($beer);

        $after_beer_count = DB::table('beers')->count();
        $this->assertEquals($after_beer_count, $before_beer_count + 1);
    }

    private function repository(): EloquentBeerRepository
    {
        return new EloquentBeerRepository(
            $this->brewer_repository->reveal(),
            $this->label_repository->reveal()
        );
    }
}
