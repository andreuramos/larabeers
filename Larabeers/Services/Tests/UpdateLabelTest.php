<?php

namespace Larabeers\Services\Tests;

use Larabeers\Domain\Common\Image;
use Larabeers\Domain\Label\Label;
use Larabeers\Domain\Label\LabelRepository;
use Larabeers\Domain\Label\Tag;
use Larabeers\External\Images\Uploader\ImageUploader;
use Larabeers\External\TagRepository;
use Larabeers\Services\UpdateLabel;
use Larabeers\Utils\GetFileType;
use Larabeers\Utils\ResizeImage;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class UpdateLabelTest extends TestCase
{
    private $prophet;
    private $label_repository;
    private $file_type_getter;
    private $image_uploader;
    private $resize_image;
    private $tag_repository;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->label_repository = $this->prophet->prophesize(LabelRepository::class);
        $this->file_type_getter = $this->prophet->prophesize(GetFileType::class);
        $this->image_uploader = $this->prophet->prophesize(ImageUploader::class);
        $this->resize_image = $this->prophet->prophesize(ResizeImage::class);
        $this->tag_repository = $this->prophet->prophesize(TagRepository::class);
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();
        $this->addToAssertionCount(count($this->prophet->getProphecies()));
    }

    /**
     * @expectedException \Larabeers\Exceptions\LabelNotFoundException
     */
    public function test_unexisting_label()
    {
        $unexisting_label_id = 1;

        $this->label_repository->findById($unexisting_label_id)
            ->shouldBeCalled()
            ->willReturn(null);

        $service = $this->getService();
        $service->execute($unexisting_label_id, "path to file");
    }

    /**
     * @expectedException \Larabeers\Exceptions\UploadFailedException
     */
    public function test_image_type_check()
    {
        $existing_label_id = 1;
        $file_path = "tmp/Not_an_image.pdf";

        $label = new Label();
        $label->id = $existing_label_id;

        $this->label_repository->findById($existing_label_id)
            ->shouldBeCalled()
            ->willReturn($label);

        $this->file_type_getter->execute($file_path)
            ->shouldBeCalled()
            ->willReturn('file/pdf');

        $service = $this->getService();
        $service->execute($existing_label_id, $file_path);
    }

    public function test_label_changes_url()
    {
        $label_id = 1;
        $file_path = "tmp/gulden_draak.jpg";
        $old_url = "https://old.sticker.url/file.jpg";
        $new_url = "https://new.sticker.url/file.jpg";
        $thumb_path = "tmp/gulden_draak_50px.png";
        $thumb_url = "https://new.sticker.url/thumb.png";

        $image = new Image();
        $image->url = $old_url;

        $new_image = new Image();
        $new_image->url = $new_url;
        $new_image->thumbnail = $thumb_url;

        $label = new Label();
        $label->id = $label_id;
        $label->sticker = $image;

        $label_with_new_image = new Label();
        $label_with_new_image->id = $label_id;
        $label_with_new_image->sticker = $new_image;

        $this->label_repository->findById($label_id)
            ->shouldBeCalled()
            ->willReturn($label);

        $this->file_type_getter->execute($file_path)
            ->shouldBeCalled()
            ->willReturn('image/jpg');

        $this->image_uploader->upload($file_path)
            ->shouldBeCalled()
            ->willReturn($new_url);

        $this->resize_image->execute($file_path, ResizeImage::THUMBNAIL_WIDTH)
            ->shouldBeCalled()
            ->willReturn($thumb_path);

        $this->image_uploader->upload($thumb_path)
            ->shouldBeCalled()
            ->willReturn($thumb_url);

        $this->label_repository->save($label_with_new_image)
            ->shouldBeCalled();

        $service = $this->getService();
        $updated_label = $service->execute($label_id, $file_path);

        $this->assertEquals($new_url, $updated_label->sticker->url);
    }

    public function test_metadata_stored()
    {
        $label_id = 1;
        $label_metadata = [
            'year' => 2020,
            'album' => 3,
            'page' => 52,
            'position' => 3
        ];

        $label = new Label();
        $label->id = $label_id;
        $label->year = 2019;
        $label->album = 2;
        $label->page = 51;
        $label->position = 2;

        $updated_label = new Label();
        $updated_label->id = $label_id;
        $updated_label->year = 2020;
        $updated_label->album = 3;
        $updated_label->page = 52;
        $updated_label->position = 3;

        $this->label_repository->findById($label_id)
            ->shouldBeCalled()
            ->willReturn($label);

        $this->label_repository->save($updated_label)
            ->shouldBeCalled();

        $service = $this->getService();
        $service->execute($label_id, null, $label_metadata);
    }

    /**
     * @expectedException \Larabeers\Exceptions\ServiceArgumentException
     * @expectedExceptionMessage Tag 0 is not a \Larabeers\Domain\Label\Tag object
     */
    public function test_tags_class_check()
    {
        $label_id = 1;

        $label = new Label();
        $label->id = 1;

        $this->label_repository->findById($label_id)
            ->shouldBeCalled()
            ->willReturn($label);

        $this->getService()->execute($label_id, null, null, ["not a tag object"]);
    }

    public function test_tags_stored()
    {
        $label_id = 1;
        $old_tags = [new Tag("these are"), new Tag("old tags")];
        $new_tags = [new Tag("and these"), new Tag("are new")];

        $label = new Label();
        $label->id = $label_id;
        $label->tags = $old_tags;

        $updated_label = new Label();
        $updated_label->id = $label_id;
        $updated_label->tags = $new_tags;

        $this->label_repository->findById($label_id)
            ->shouldBeCalled()
            ->willReturn($label);

        $this->label_repository->save($updated_label)
            ->shouldBeCalled();

        $this->getService()->execute($label_id, null, null, $new_tags);
    }

    private function getService()
    {
        return new UpdateLabel(
            $this->label_repository->reveal(),
            $this->file_type_getter->reveal(),
            $this->image_uploader->reveal(),
            $this->tag_repository->reveal(),
            $this->resize_image->reveal()
        );
    }
}
