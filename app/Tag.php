<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function labels()
    {
        return $this->belongsToMany('App\Label');
    }
}
