<?php

namespace Larabeers\Domain\Location;

interface CountryMapper
{
    public function execute(string $country_name): string;
}
