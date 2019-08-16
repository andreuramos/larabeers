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

    public function labels()
    {
        return $this->hasMany('App\Label');
    }

    public static function paginate($query,$page_size=10)
    {
        return self::where('normalized_name', 'ilike', "%$query%")->paginate($page_size);
    }

    public static function search($query)
    {
        return self::where('normalized_name','ilike',"%$query%")->get();
    }

    public static function distinct_types()
    {
        return self::select('type')->distinct()->get();
    }
}
