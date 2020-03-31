<?php

namespace Larabeers\External;

use App\Sticker;
use Larabeers\Entities\Label;
use App\Label as EloquentLabel;

class LabelRepository
{
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
            dd($sticker);
            if (!$sticker) {
                $sticker = new Sticker();
                $sticker->label_id = $eloquent_label->id;
            }
            dd($sticker);
            $sticker->path = $label->sticker->url;
            $sticker->save();
        }
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
}
