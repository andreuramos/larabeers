<?php

use Larabeers\Domain\Common\Year;
use Larabeers\Exceptions\InvalidValueException;
use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    /**
     * @expectedException Larabeers\Exceptions\InvalidValueException
     */
    public function test_not_a_number_throws_exception()
    {
        new Year("not a year");
    }

    /**
     * @expectedException  Larabeers\Exceptions\InvalidValueException
     */
    public function test_negative_year_throws_exception()
    {
        new Year(-30);
    }

    /**
     * @expectedException  Larabeers\Exceptions\InvalidValueException
     */
    public function test_far_future_year_throws_exception()
    {
        new Year(40000);
    }

    public function test_integer_int_value_can_be_accessed()
    {
        $year = new Year(2020);

        $this->assertEquals($year->getYear(), 2020);
    }

    public function test_string_year_stored_as_integer()
    {
        $year = new Year("2020");

        $this->assertEquals($year->getYear(), 2020);
        $this->assertIsInt($year->getYear());
    }
}
