<?php

namespace Larabeers\Services\Tests;

use Larabeers\Entities\Beer;
use Larabeers\External\BeerRepository;
use Larabeers\Services\UpdateBeer;
use Larabeers\Utils\NormalizeString;
use \PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class UpdateBeerTest extends TestCase
{
    private $prophet;
    private $beer_repository;
    private $normalize_string;

    public function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->beer_repository = $this->prophet->prophesize(BeerRepository::class);
        $this->normalize_string = $this->prophet->prophesize(NormalizeString::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    /**
     * @expectedException \Larabeers\Exceptions\BeerNotFoundException
     * @expectedExceptionMessage beer 0 not found
     */
    public function test_unexisting_beer()
    {
        $this->beer_repository
            ->findById(0)
            ->shouldBeCalled()
            ->willReturn(null);

        $service = $this->getService();
        $service->execute(0, "");
    }

    public function test_changing_name_updates_normalized_too()
    {
        $old_beer = new Beer();
        $old_beer->id = 1;
        $old_beer->name = "OldName";

        $new_beer = new Beer();
        $new_beer->id = 1;
        $new_beer->name="NewName";
        $new_beer->normalized_name = "newname";

        $this->beer_repository
            ->findById(1)
            ->shouldBeCalled()
            ->willReturn($old_beer);
        $this->normalize_string
            ->execute("NewName")
            ->shouldBeCalled()
            ->willReturn("newname");
        $this->beer_repository
            ->save($new_beer)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute(1,"NewName");

        $this->beer_repository->save($new_beer)->shouldHaveBeenCalled();
    }

    private function getService(): UpdateBeer
    {
        return new UpdateBeer(
            $this->beer_repository->reveal(),
            $this->normalize_string->reveal()
        );
    }
}
