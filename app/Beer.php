<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    protected $fillable = ["name", "type"];

    public function brewers()
    {
        return $this->belongsToMany('App\Brewer');
    }
}
