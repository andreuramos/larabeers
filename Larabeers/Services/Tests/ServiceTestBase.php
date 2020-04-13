<?php

namespace Larabeers\Services\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class ServiceTestBase extends TestCase
{
    protected $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet();
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }
}
