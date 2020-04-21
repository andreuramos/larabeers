<?php

namespace Larabeers\Services\Tests;

use Larabeers\Entities\Brewer;
use Larabeers\Entities\City;
use Larabeers\Entities\Country;
use Larabeers\External\BrewerRepository;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\Services\UpdateBrewer;
use Larabeers\Utils\NormalizeString;

class UpdateBrewerTest extends ServiceTestBase
{
    private $brewer_repository;
    private $image_uploader;
    private $normalize_string;

    private $city;
    private $country;
    private $name = "Brewer Name";
    private $normalized_name = "brewer name";


    public function setUp()
    {
        parent::setUp();
        $this->brewer_repository = $this->prophet->prophesize(BrewerRepository::class);
        $this->image_uploader = $this->prophet->prophesize(ImageUploader::class);
        $this->normalize_string = $this->prophet->prophesize(NormalizeString::class);

        $this->country = new Country("Brewland");
        $this->city = new City("brewtown", $this->country);
    }

    /**
     * @expectedException \Larabeers\Exceptions\BrewerNotFoundException
     */
    public function test_unexisting_brewer()
    {
        $this->brewer_repository->findById(0)
            ->shouldBeCalled()
            ->willReturn(null);

        $this->getService()->execute(0, "-",$this->city);
    }

    /**
     * @expectedException \Larabeers\Exceptions\ServiceArgumentException
     */
    public function test_empty_name()
    {
        $this->getService()->execute(1, "" , $this->city);
    }

    public function test_object_without_data()
    {
        $brewer = new Brewer();
        $brewer->name = $this->name;
        $brewer->normalized_name = $this->normalized_name;
        $brewer->city = $this->city;

        $new_city = new City("Brewville", new Country("Brewlandia"));
        
        $new_brewer = new Brewer();
        $new_brewer->name = "new name";
        $new_brewer->normalized_name = "new name";
        $new_brewer->city = $new_city;

        $this->brewer_repository->findById(1)
            ->shouldBeCalled()
            ->willReturn($brewer);
        $this->normalize_string->execute("new name")
            ->shouldBeCalled()
            ->willReturn("new name");
        $this->brewer_repository->save($new_brewer)
            ->shouldBeCalled();

        $this->getService()->execute(1, "new name", $new_city);
    }

    public function test_object_with_data()
    {

    }

    public function getService()
    {
        return new UpdateBrewer(
            $this->brewer_repository->reveal(),
            $this->image_uploader->reveal(),
            $this->normalize_string->reveal()
        );
    }
}
