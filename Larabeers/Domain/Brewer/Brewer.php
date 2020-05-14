<?php

namespace Larabeers\Domain\Brewer;

use Larabeers\Domain\Location\City;
use Larabeers\Domain\Common\Image;

class Brewer
{
    public $id;
    public $name;
    public $normalized_name;
    public ?City $city;
    public Image $logo;
    public $latitude;
    public $longitude;
    public $address;
    public $website;

    public function __construct()
    {
        $this->logo = new Image();
    }
}
