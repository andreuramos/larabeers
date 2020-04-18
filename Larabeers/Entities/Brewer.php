<?php

namespace Larabeers\Entities;

class Brewer
{
    public $id;
    public $name;
    public $normalized_name;
    public ?City $city;
    public ?Image $logo;
    public $latitude;
    public $longitude;
    public $address;
    public $website;
}
