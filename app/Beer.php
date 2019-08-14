<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beer extends Model
{
    public function brewers()
    {
        return $this->belongsToMany('App\Brewer');
    }
}
