<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Common\Image;
use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Domain\Label\Tag;
use Larabeers\Exceptions\UploadFailedException;
use Larabeers\Domain\Common\ImageUploader;
use Larabeers\Services\CreateLabelToBeer;
use Larabeers\Utils\GetFileType;
use Larabeers\Utils\ResizeImage;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CreateLabelToBeerTest extends TestCase
{
    const IMAGE_JPG = 'image/jpg';
    private $prophet;
    private $get_file_type;
    private $image_uploader;
    private $label_repository;
    private $resize_image;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->get_file_type = $this->prophet->prophesize(GetFileType::class);
        $this->image_uploader = $this->prophet->prophesize(ImageUploader::class);
        $this->label_repository = $this->prophet->prophesize(LabelRepository::class);
        $this->resize_image = $this->prophet->prophesize(ResizeImage::class);
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
     * @expectedExceptionMessage Unsupported image type: document/pdf
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
        $large_path = "/tmp/image/large.jpg";

        $this->get_file_type->execute($tmp_file_path)
            ->shouldBeCalled()
            ->willReturn(self::IMAGE_JPG);

        $this->resize_image->execute($tmp_file_path, ResizeImage::LARGE_WIDTH)
            ->shouldBeCalled()
            ->willReturn($large_path);

        $this->image_uploader->upload($large_path)
            ->shouldBeCalled()
            ->willThrow(new UploadFailedException("failed to upload resource"));

        $service = $this->getService();
        $service->execute(1, $tmp_file_path, []);
    }

    public function test_image_is_uploaded_then_label_created_with_url()
    {
        $image_path = "/tmp/image/path.jpg";
        $large_path = "/tmp/image/large.jpg";
        $thumb_path = "/tmp/image/thumb.png";
        $image = new Image();
        $image_url = "http://cloud.storage.url/resource/hash";
        $thumb_url = "http://cloud.storage.url/resource/thumb";
        $image->url = $image_url;
        $image->thumbnail = $thumb_url;
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

        $this->resize_image->execute($image_path, ResizeImage::LARGE_WIDTH)
            ->shouldBeCalled()
            ->willReturn($large_path);

        $this->image_uploader->upload($large_path)
            ->shouldBeCalled()
            ->willReturn($image_url);

        $this->resize_image->execute($image_path, ResizeImage::THUMBNAIL_WIDTH)
            ->shouldBeCalled()
            ->willReturn($thumb_path);

        $this->image_uploader->upload($thumb_path)
            ->shouldBeCalled()
            ->willReturn($thumb_url);

        $this->label_repository->save($label)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute(1, $image_path, $label_data);
    }

    public function test_tags_are_stored()
    {
        $image_path = "/tmp/image/path.jpg";
        $large_path = "/tmp/image/large.jpg";
        $thumb_path = "/tmp/image/thumb.png";
        $image = new Image();
        $image_url = "http://cloud.storage.url/resource/hash";
        $thumb_url = "http://cloud.storage.url/resource/thumb";
        $image->url = $image_url;
        $image->thumbnail = $thumb_url;
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

        $this->resize_image->execute($image_path, ResizeImage::LARGE_WIDTH)
            ->shouldBeCalled()
            ->willReturn($large_path);

        $this->image_uploader->upload($large_path)
            ->shouldBeCalled()
            ->willReturn($image_url);

        $this->resize_image->execute($image_path, ResizeImage::THUMBNAIL_WIDTH)
            ->shouldBeCalled()
            ->willReturn($thumb_path);

        $this->image_uploader->upload($thumb_path)
            ->shouldBeCalled()
            ->willReturn($thumb_url);

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
            $this->label_repository->reveal(),
            $this->resize_image->reveal()
        );
    }
}
