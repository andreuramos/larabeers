<?php

namespace Larabeers\Domain\Location;

interface FlagRepository
{
    public static function get(string $country_code): ?string;
}
