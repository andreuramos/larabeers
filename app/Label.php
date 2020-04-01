<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = ["beer_id", "year", "month", "album", "page", "position"];

    public $timestamps = false;

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function stickers()
    {
        return $this->hasMany('App\Sticker');
    }

    public function path()
    {
        if ($this->stickers->count()) {
            return $this->stickers->first()->path;
        }
        return null;
    }
}
