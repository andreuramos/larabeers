<?php

namespace Larabeers\External\Tests;

use Larabeers\External\GoogleMapsCountryMapper;
use PHPUnit\Framework\TestCase;

class GoogleMapsCountryMapperTest extends TestCase
{
    public function test_unmapped_name_is_returned()
    {
        $service = new GoogleMapsCountryMapper();
        $unmappedName = "Spain";

        $mappedName = $service->execute($unmappedName);

        $this->assertEquals($unmappedName, $mappedName);
    }

    public function test_mapped_name_is_returned()
    {
        $service = new GoogleMapsCountryMapper();
        $unmappedName = "USA";

        $mappedName = $service->execute($unmappedName);

        $this->assertEquals("United States", $mappedName);
    }
}
