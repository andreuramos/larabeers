<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brewer extends Model
{
    protected $fillable = ["name", "country", "city"];

    public function beers()
    {
        return $this->belongsToMany('App\Beer');
    }

    public static function distinctCountries()
    {
        return self::select('country')->distinct()->get();
    }
}
