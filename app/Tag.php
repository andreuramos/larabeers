<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = ["text"];

    public function labels()
    {
        return $this->belongsToMany('App\Label');
    }
}
