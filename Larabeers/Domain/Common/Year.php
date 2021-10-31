<?php

namespace Larabeers\Domain\Common;

use Larabeers\Exceptions\InvalidValueException;

class Year
{
    private const MAX_YEAR = 3000;
    private const MIN_YEAR = 0;

    private int $value;

    public function __construct($year)
    {
        $value = null;
        if (is_int($year)) {
            $value = $year;
        } elseif (is_string($year)) {
            if (is_numeric($year)) {
                $value = (int) $year;
            }
        } else {
            throw new InvalidValueException("$year is not a valid year");
        }

        if ($value === null || $value < self::MIN_YEAR || $value > self::MAX_YEAR) {
            throw new InvalidValueException("$year is not a valid year");
        }

        $this->value = $value;
    }

    public function getYear(): int
    {
        return $this->value;
    }
}
