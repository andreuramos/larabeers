<?php

namespace Larabeers\External;

use Larabeers\Domain\Location\CountryMapper;

class GoogleMapsCountryMapper implements CountryMapper
{
    private const MAPPED_COUNTRIES = [
        "USA" => "United States",
        "England" => "United Kingdom",
        "Slovak Republic" => "Slovakia",
    ];

    public function execute(string $country_name): string
    {
        if (array_key_exists($country_name, self::MAPPED_COUNTRIES)) {
            return self::MAPPED_COUNTRIES[$country_name];
        }

        return $country_name;
    }
}
