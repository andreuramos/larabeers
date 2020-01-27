<?php

namespace Larabeers\External;

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

        if ($label->image) {

        }
    }

    private static function populateEloquentLabel(EloquentLabel $eloquent_label, Label $label): EloquentLabel
    {
        $eloquent_label->year = $label->year;

        return $eloquent_label;
    }

    private static function entityToEloquentLabel(Label $label): EloquentLabel
    {
        $eloquent_label = new EloquentLabel();

        $eloquent_label->beer_id = $label->beer_id;
        $eloquent_label->year = $label->year;
    }
}
