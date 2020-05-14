<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Beer\Style;
use Larabeers\External\StyleRepository;
use Larabeers\Services\SearchStyle;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class SearchStyleTest extends TestCase
{
    private $prophet;
    private $style_repository;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->style_repository = $this->prophet->prophesize(StyleRepository::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    public function test_no_results()
    {
        $query = "not an existing style";
        $this->style_repository->search($query)
            ->shouldBeCalled()
            ->willReturn([]);

        $service = $this->getService();
        $actual = $service->execute($query);

        $this->assertEquals([], $actual);
    }

    public function test_some_results_are_styles()
    {
        $query = "match";

        $style1 = new Style("match number 1");
        $style2 = new Style("match number 2");

        $this->style_repository->search($query)
            ->shouldBeCalled()
            ->willReturn([$style1, $style2]);

        $service = $this->getService();
        $actual = $service->execute($query);

        $this->assertEquals([$style1, $style2], $actual);
    }

    private function getService()
    {
        return new SearchStyle(
            $this->style_repository->reveal()
        );
    }
}
