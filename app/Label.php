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
}
