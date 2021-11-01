<?php

namespace Larabeers\Services;

use Larabeers\Domain\Location\City;
use Larabeers\Domain\Location\Country;

class CityFactory
{
    public static function build(string $city_name, string $country_name): City
    {
        return new City($city_name, new Country($country_name));
    }
}
