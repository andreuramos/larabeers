<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Larabeers\External\Images\FlagpediaFlagRepository;

class Beer extends Model
{
    protected $fillable = ["name", "type"];

    public function brewers()
    {
        return $this->belongsToMany('App\Brewer');
    }

    public function labels()
    {
        return $this->hasMany('App\Label');
    }

    public function flag()
    {
        $country = $this->brewers()->first()->country;
        return FlagpediaFlagRepository::get($country);
    }

    public static function random(int $n): Collection
    {
        return self::inRandomOrder()->take($n)->get();
    }

    public static function search($query)
    {
        return self::where('normalized_name', 'ilike', "%$query%")->get();
    }

    public static function distinctTypes()
    {
        return self::select('type')->distinct()->get();
    }
}
