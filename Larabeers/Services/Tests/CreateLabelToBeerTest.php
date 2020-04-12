<?php

namespace Larabeers\Services\Tests;

use Larabeers\Entities\Image;
use Larabeers\Entities\Label;
use Larabeers\Entities\Tag;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\External\LabelRepository;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Utils\GetFileType;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CreateLabelToBeerTest extends TestCase
{
    const IMAGE_JPG = 'image/jpg';
    private $prophet;
    private $get_file_type;
    private $image_uploader;
    private $label_repository;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->get_file_type = $this->prophet->prophesize(GetFileType::class);
        $this->image_uploader = $this->prophet->prophesize(ImageUploader::class);
        $this->label_repository = $this->prophet->prophesize(LabelRepository::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No image provided
     */
    public function test_no_image_source()
    {
        $image_path = "";

        $service = $this->getService();
        $service->execute(1, $image_path, []);
    }

    /**
     * @expectedException \Larabeers\Exceptions\UploadFailedException
     * @expectedExceptionMessage Unsupported image type
     */
    public function test_image_is_actually_an_image()
    {
        $tmp_file_path = "/tmp/image/path.jpg";

        $this->get_file_type->execute($tmp_file_path)
            ->shouldBeCalled()
            ->willReturn("document/pdf");

        $service = $this->getService();
        $service->execute(1, $tmp_file_path, []);
    }

    /**
     * @expectedException \Larabeers\Exceptions\UploadFailedException
     * @expectedExceptionMessage failed to upload resource
     */
    public function test_image_failed_to_upload()
    {
        $tmp_file_path = "/tmp_file_path/image/path.jpg";

        $this->get_file_type->execute($tmp_file_path)
            ->shouldBeCalled()
            ->willReturn(self::IMAGE_JPG);

        $this->image_uploader->upload($tmp_file_path)
            ->shouldBeCalled()
            ->willThrow(new UploadFailedException("failed to upload resource"));

        $service = $this->getService();
        $service->execute(1, $tmp_file_path, []);
    }

    public function test_image_is_uploaded_then_label_created_with_url()
    {
        $image_path = "/tmp/image/path.jpg";
        $image = new Image();
        $image->url = "http://cloud.storage.url/resource/hash";
        $label_data = [
            'year' => 2020,
            'album' => 1,
            'page' => 1,
            'position' => 1
        ];
        $label = new Label();
        $label->beer_id = 1;
        $label->year = 2020;
        $label->album = 1;
        $label->page = 1;
        $label->position = 1;
        $label->sticker = $image;

        $this->get_file_type->execute($image_path)
            ->shouldBeCalled()
            ->willReturn(self::IMAGE_JPG);

        $this->image_uploader->upload($image_path)
            ->shouldBeCalled()
            ->willReturn($image);

        $this->label_repository->save($label)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute(1, $image_path, $label_data);
    }

    public function test_tags_are_stored()
    {
        $image_path = "/tmp/image/path.jpg";
        $image = new Image();
        $image->url = "http://cloud.storage.url/resource/hash";
        $label_data = [
            'year' => 2020,
            'album' => 1,
            'page' => 1,
            'position' => 1
        ];
        $tags = [new Tag("some"), new Tag("tags")];
        $label = new Label();
        $label->beer_id = 1;
        $label->year = 2020;
        $label->album = 1;
        $label->page = 1;
        $label->position = 1;
        $label->sticker = $image;
        $label->tags = $tags;

        $this->get_file_type->execute($image_path)
            ->shouldBeCalled()
            ->willReturn(self::IMAGE_JPG);

        $this->image_uploader->upload($image_path)
            ->shouldBeCalled()
            ->willReturn($image);

        $this->label_repository->save($label)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute(1, $image_path, $label_data, $tags);
    }

    private function getService()
    {
        return new CreateLabelToBeer(
            $this->get_file_type->reveal(),
            $this->image_uploader->reveal(),
            $this->label_repository->reveal()
        );
    }
}
