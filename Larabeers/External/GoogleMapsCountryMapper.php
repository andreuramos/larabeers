<?php

namespace Larabeers\External;

use Larabeers\Domain\Location\CountryMapper;

class GoogleMapsCountryMapper implements CountryMapper
{
    public function execute(string $country_name): string
    {
        if ($country_name === "USA") {
            $country_name = "United States";
        } elseif ($country_name === "England") {
            $country_name = "United Kingdom";
        } elseif ($country_name === "Slovak Republic") {
            $country_name = "Slovakia";
        }
        return $country_name;
    }
}
