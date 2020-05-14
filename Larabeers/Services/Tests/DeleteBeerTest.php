<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Beer\Beer;
use Larabeers\External\EloquentBeerRepository;
use Larabeers\Services\DeleteBeer;

class DeleteBeerTest extends ServiceTestBase
{
    private $beer_repository;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->beer_repository = $this->prophet->prophesize(EloquentBeerRepository::class);
    }

    /**
     * @expectedException \Larabeers\Exceptions\BeerNotFoundException
     */
    public function test_unexisting_beer()
    {
        $unexisting_beer = new Beer();

        $service = $this->getService();
        $service->execute($unexisting_beer);
    }

    public function test_existing_beer_calls_repository()
    {
        $stored_beer = new Beer();
        $stored_beer->id = 1;

        $this->beer_repository->delete($stored_beer)
            ->shouldBeCalled();

        $this->getService()->execute($stored_beer);
    }

    private function getService(){
        return new DeleteBeer(
            $this->beer_repository->reveal()
        );
    }
}
