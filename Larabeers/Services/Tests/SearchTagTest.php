<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Label\Tag;
use Larabeers\External\TagRepository;
use Larabeers\Services\SearchTag;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class SearchTagTest extends TestCase
{
    private $prophet;
    private $tag_repository;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->tag_repository = $this->prophet->prophesize(TagRepository::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    public function test_no_results()
    {
        $this->tag_repository->search("no-match")
            ->shouldBeCalled()
            ->willReturn([]);

        $service = $this->getService();
        $result = $service->execute("no-match");
        $this->assertEquals([], $result);
    }

    public function test_results_are_tags()
    {
        $tag = new Tag("gift");

        $this->tag_repository->search("gif")
            ->shouldBeCalled()
            ->willReturn([$tag]);

        $result = $this->getService()->execute("gif");
        $this->assertEquals(1, count($result));
        $this->assertInstanceOf(Tag::class, $result[0]);

    }

    private function getService()
    {
        return new SearchTag(
            $this->tag_repository->reveal()
        );
    }
}
