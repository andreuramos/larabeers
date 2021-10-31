<?php

namespace Larabeers\Services\Tests;

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
        $this->beer_repository->countByYear(self::YEAR)->willReturn(2);
        $service = $this->getService();

        $result = $service->execute(new Year(self::YEAR));

        $this->assertIsInt($result);
    }

    private function getService()
    {
        return new CountBeersByYear($this->beer_repository->reveal());
    }

}
