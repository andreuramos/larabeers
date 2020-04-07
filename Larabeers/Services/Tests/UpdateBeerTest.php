<?php

namespace Larabeers\Services\Tests;

use Larabeers\Entities\Beer;
use Larabeers\Entities\Brewer;
use Larabeers\Entities\Style;
use Larabeers\External\BeerRepository;
use Larabeers\External\BrewerRepository;
use Larabeers\Services\UpdateBeer;
use Larabeers\Utils\NormalizeString;
use \PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class UpdateBeerTest extends TestCase
{
    private $prophet;
    private $beer_repository;
    private $brewer_repository;
    private $normalize_string;

    public function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->beer_repository = $this->prophet->prophesize(BeerRepository::class);
        $this->brewer_repository = $this->prophet->prophesize(BrewerRepository::class);
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
        $service->execute(0, "", 0, new Style("anything"));
    }

    /**
     * @expectedException \Larabeers\Exceptions\BrewerNotFoundException
     */
    public function test_unexisting_brewer()
    {
        $old_beer = new Beer();
        $old_beer->id = 1;
        $old_beer->name = "OLDNAME";

        $this->beer_repository->findById(1)
            ->shouldBeCalled()
            ->willReturn($old_beer);

        $this->brewer_repository->findById(2)
            ->shouldBeCalled()
            ->willReturn(null);

        $this->getService()->execute(1,"OLDNAME",2, new Style("anything"));
    }

    public function test_changing_name_updates_normalized_too()
    {
        $old_brewer = new Brewer();
        $old_brewer->id = 1;
        $new_brewer_id = 2;
        $new_brewer = new Brewer();
        $new_brewer->id = $new_brewer_id;

        $old_style = new Style("old style ale");
        $old_beer = new Beer();
        $old_beer->id = 1;
        $old_beer->brewers[] = $old_brewer;
        $old_beer->name = "OldName";
        $old_beer->style = $old_style;

        $new_style = new Style("new style lager");
        $new_beer = new Beer();
        $new_beer->id = 1;
        $new_beer->brewers[] = $new_brewer;
        $new_beer->name="NewName";
        $new_beer->normalized_name = "newname";
        $new_beer->style = $new_style;

        $this->beer_repository
            ->findById(1)
            ->shouldBeCalled()
            ->willReturn($old_beer);
        $this->brewer_repository
            ->findById($new_brewer_id)
            ->shouldBeCalled()
            ->willReturn($new_brewer);
        $this->normalize_string
            ->execute("NewName")
            ->shouldBeCalled()
            ->willReturn("newname");
        $this->beer_repository
            ->save($new_beer)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute(1,"NewName", $new_brewer_id, $new_style);

        $this->beer_repository->save($new_beer)->shouldHaveBeenCalled();
    }

    private function getService(): UpdateBeer
    {
        return new UpdateBeer(
            $this->beer_repository->reveal(),
            $this->brewer_repository->reveal(),
            $this->normalize_string->reveal()
        );
    }
}
