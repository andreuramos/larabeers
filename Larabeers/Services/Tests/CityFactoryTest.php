<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Location\City;
use Larabeers\Services\CityFactory;
use PHPUnit\Framework\TestCase;

class CityFactoryTest extends TestCase
{
    public function test_returns_a_city()
    {
        $city = CityFactory::build("Palma", "Spain");

        $this->assertInstanceOf(City::class, $city);
    }

    public function test_city_has_specified_name()
    {
        $city = CityFactory::build("Palma", "Spain");

        $this->assertEquals("Palma", $city->name);
    }

    public function test_city_has_specifed_country_name()
    {
        $city = CityFactory::build("Palma", "Spain");

        $this->assertEquals("Spain", $city->country->name);
    }
}
