<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Beer\Beer;
use Larabeers\Domain\Beer\BeerRepository;
use Larabeers\Domain\Brewer\Brewer;
use Larabeers\Domain\Beer\Style;
use Larabeers\External\EloquentBrewerRepository;
use Larabeers\Services\CreateBeer;
use Larabeers\Utils\NormalizeString;

class CreateBeerTest extends ServiceTestBase
{
    private $beer_repository;
    private $brewer_repository;
    private $normalize_string;

    public function setUp()
    {
        parent::setUp();
        $this->beer_repository = $this->prophet->prophesize(BeerRepository::class);
        $this->brewer_repository = $this->prophet->prophesize(EloquentBrewerRepository::class);
        $this->normalize_string = $this->prophet->prophesize(NormalizeString::class);
    }

    /**
     * @expectedException \Larabeers\Exceptions\ServiceArgumentException
     * @expectedExceptionMessage Name must not be empty
     */
    public function test_empty_name()
    {
        $this->getService()->execute("", 0, new Style(""));
    }

    /**
     * @expectedException \Larabeers\Exceptions\BrewerNotFoundException
     */
    public function test_unexisting_brewer()
    {
        $this->brewer_repository->findById(0)
            ->shouldBeCalled()
            ->willReturn(null);

        $this->getService()->execute("new beer", 0, new Style(""));
    }

    /**
     * @expectedException \Larabeers\Exceptions\DuplicatedBeerException
     */
    public function test_duplicated_beer()
    {
        $name = "already exists";
        $brewer = new Brewer();
        $brewer->id = 1;

        $this->brewer_repository->findById(1)
            ->shouldBeCalled()
            ->willReturn($brewer);

        $this->beer_repository->exists($name, $brewer)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->getService()->execute($name, 1, new Style(""));
    }

    public function test_returns_id()
    {
        $name = "New Beer";
        $brewer = new Brewer();
        $brewer->id = 1;
        $style = new Style("amber lager");

        $beer = new Beer();
        $beer->name = $name;
        $beer->brewers[] = $brewer;
        $beer->style = $style;
        $beer->normalized_name = "new beer";

        $this->brewer_repository->findById(1)
            ->shouldBeCalled()
            ->willReturn($brewer);

        $this->beer_repository->exists($name, $brewer)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->normalize_string->execute($name)
            ->shouldBeCalled()
            ->willReturn("new beer");

        $this->beer_repository->save($beer)
            ->shouldBeCalled()
            ->willReturn(1);

        $result = $this->getService()->execute($name, 1, $style);
        $this->assertIsInt($result);
    }

    private function getService()
    {
        return new CreateBeer(
            $this->beer_repository->reveal(),
            $this->brewer_repository->reveal(),
            $this->normalize_string->reveal()
        );
    }
}
