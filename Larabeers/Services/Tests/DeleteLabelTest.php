<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Services\DeleteLabel;
use PHPUnit\Framework\TestCase;

class DeleteLabelTest extends TestCase
{
    private $label_repository;

    public function setUp()
    {
        $this->label_repository = $this->prophesize(LabelRepository::class);
    }

    /**
     * @expectedException \Larabeers\Exceptions\LabelNotFoundException
     */
    public function test_unexisting_label()
    {
        $service = $this->getService();

        $service->execute(new Label());
    }

    public function test_exising_label_calls_repository()
    {
        $label = new Label();
        $label->id = 1;
        $this->label_repository->delete($label)->shouldBeCalled();
        $service = $this->getService();

        $service->execute($label);
    }

    private function getService(): DeleteLabel
    {
        return new DeleteLabel(
            $this->label_repository->reveal()
        );
    }
}
