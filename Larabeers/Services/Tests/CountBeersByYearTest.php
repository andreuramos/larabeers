<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\BeerCollection;
use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Common\Year;
use Larabeers\Services\CountBeersByYear;

class CountBeersByYearTest extends ServiceTestBase
{
    const YEAR = 2020;
    private $beer_repository;

    public function setUp()
    {
        parent::setUp();
        $this->beer_repository = $this->prophet->prophesize(BeerRepository::class);
    }

    public function test_returns_integer()
    {
        $this->beer_repository->findByYear(self::YEAR)->willReturn(new BeerCollection());
        $service = $this->getService();

        $result = $service->execute(new Year(self::YEAR));

        $this->assertIsInt($result);
    }

    public function test_returns_the_count_of_collection()
    {
        $collection = new BeerCollection();
        $collection->add(new Beer());
        $this->beer_repository->findByYear(self::YEAR)->willReturn($collection);
        $service = $this->getService();

        $result = $service->execute(new Year(self::YEAR));

        $this->assertEquals(1, $result);
    }

    private function getService()
    {
        return new CountBeersByYear($this->beer_repository->reveal());
    }

}
