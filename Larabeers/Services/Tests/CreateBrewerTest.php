<?php

namespace Larabeers\Services\Tests;

use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Entities\Country;
use Larabeers\External\BrewerRepository;
use Larabeers\Services\CreateBrewer;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CreateBrewerTest extends TestCase
{
    private $prophet;
    private $brewer_repository;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->brewer_repository = $this->prophet->prophesize(BrewerRepository::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    /**
     * @expectedException \Larabeers\Exceptions\ServiceArgumentException
     */
    public function test_empty_name()
    {
        $name = "";
        $country = new Country("Spain");
        $city = new City("Es Pla de na Tesa", $country);

        $service = $this->getService();
        $service->execute($name, $city);
    }

    public function test_returns_id()
    {
        $name = "Moixa Brewing";
        $country = new Country("Spain");
        $city = new City("Es pla de na Tesa", $country);

        $brewer = new Brewer();
        $brewer->name = $name;
        $brewer->city = $city;

        $this->brewer_repository->save($brewer)
            ->shouldBeCalled()
            ->willReturn(2);

        $service = $this->getService();
        $id = $service->execute($name,$city);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0,$id);

    }

    private function getService()
    {
        return new CreateBrewer(
            $this->brewer_repository->reveal()
        );
    }
}
