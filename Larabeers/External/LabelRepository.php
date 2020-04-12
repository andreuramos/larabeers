<?php

namespace Larabeers\External;

use Larabeers\Entities\Image;
use Larabeers\Entities\Label;
use Larabeers\Entities\Tag;
use App\Label as EloquentLabel;
use App\Sticker;
use App\Tag as EloquentTag;

class LabelRepository
{
    public function findById(int $id): ?Label
    {
        $eloquent_label = EloquentLabel::find($id);
        if (!$eloquent_label) {
            return null;
        }

        return self::eloquentToEntityLabel($eloquent_label);
    }

    public function save(Label $label)
    {
        if ($label->id) {
            $eloquent_label = EloquentLabel::find($label->id);
            $eloquent_label = self::populateEloquentLabel($eloquent_label, $label);
        } else {
            $eloquent_label = self::entityToEloquentLabel($label);
        }

        $eloquent_label->save();

        if ($label->sticker) {
            $sticker = $eloquent_label->stickers->first();

            if (!$sticker) {
                $sticker = new Sticker();
                $sticker->label_id = $eloquent_label->id;
            }

            $sticker->path = $label->sticker->url;
            $sticker->save();
        }

        $this->syncTags($eloquent_label, $label->tags);
    }

    private static function populateEloquentLabel(EloquentLabel $eloquent_label, Label $label): EloquentLabel
    {
        $eloquent_label->beer_id = $label->beer_id;
        $eloquent_label->year = $label->year;
        $eloquent_label->album = $label->album;
        $eloquent_label->page = $label->page;
        $eloquent_label->position = $label->position;

        return $eloquent_label;
    }

    private static function entityToEloquentLabel(Label $label): EloquentLabel
    {
        $eloquent_label = new EloquentLabel();

        if ($label->id) $eloquent_label->id = $label->id;
        $eloquent_label->beer_id = $label->beer_id;
        $eloquent_label->year = $label->year;
        $eloquent_label->album = $label->album;
        $eloquent_label->page = $label->page;
        $eloquent_label->position = $label->position;

        return $eloquent_label;
    }

    private static function eloquentToEntityLabel(EloquentLabel $eloquent_label): Label
    {
        $label = new Label();

        $label->id = $eloquent_label->id;
        $label->beer_id = $eloquent_label->beer_id;
        $label->year = $eloquent_label->year;
        $label->album = $eloquent_label->album;
        $label->page = $eloquent_label->page;
        $label->position = $eloquent_label->position;
        $label->tags = [];

        if (count($eloquent_label->stickers)) {
            $sticker = new Image();
            $sticker->url = $eloquent_label->stickers->first()->path;
            $label->sticker = $sticker;
        } else {
            $label->sticker = null;
        }

        if (count($eloquent_label->tags)) {
            foreach ($eloquent_label->tags as $eloquent_tag) {
                $label->tags[] = new Tag($eloquent_tag->text);
            }
        }

        return $label;
    }

    private function syncTags(EloquentLabel $eloquent_label, array $tags)
    {
        $tag_ids = [];

        foreach ($tags as $tag) {
            $eloquent_tag = EloquentTag::where('text', $tag->text)->first();
            if (!$eloquent_tag) {
                $eloquent_tag = new EloquentTag();
                $eloquent_tag->text = $tag->text;
                $eloquent_tag->save();
            }
            $tag_ids[] = $eloquent_tag->id;
        }

        $eloquent_label->tags()->sync($tag_ids);
    }
}
