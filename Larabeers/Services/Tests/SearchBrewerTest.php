<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Brewer\Brewer;
use Larabeers\External\BrewerRepository;
use Larabeers\Services\SearchBrewer;
use Larabeers\Utils\NormalizeString;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class SearchBrewerTest extends TestCase
{
    private $prophet;
    private $brewer_repository;
    private $normalize_string;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->brewer_repository = $this->prophet->prophesize(BrewerRepository::class);
        $this->normalize_string = $this->prophet->prophesize(NormalizeString::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    public function test_no_results()
    {
        $noresultsquery = "no results query";
        $this->normalize_string->execute($noresultsquery)
            ->shouldBeCalled()
            ->willReturn($noresultsquery);
        $this->brewer_repository->search($noresultsquery)
            ->shouldBeCalled()
            ->willReturn([]);

        $service = $this->getService();
        $results = $service->execute($noresultsquery);

        $this->assertEquals([], $results);
    }

    public function test_one_result()
    {
        $query = "Moixa Brew";
        $normalized_query = "moixa brew";

        $moixa_brewing = new Brewer();
        $moixa_brewing->id = 666;
        $moixa_brewing->country = "Spain";
        $moixa_brewing->name = "Moixa Brewing";

        $this->normalize_string->execute($query)
            ->shouldBeCalled()
            ->willReturn($normalized_query);
        $this->brewer_repository->search($normalized_query)
            ->shouldBeCalled()
            ->willReturn([$moixa_brewing]);

        $service = $this->getService();
        $results = $service->execute($query);

        $this->assertEquals([$moixa_brewing], $results);
    }

    private function getService()
    {
        return new SearchBrewer(
            $this->brewer_repository->reveal(),
            $this->normalize_string->reveal()
        );
    }


}
