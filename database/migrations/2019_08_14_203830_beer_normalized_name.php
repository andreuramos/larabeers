<?php

use App\Beer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BeerNormalizedName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beers', function (Blueprint $t) {
            $t->string('normalized_name')->after('name')->nullable();
        });

        foreach (Beer::all() as $beer) {
            $beer->normalized_name = \Larabeers\Utils\NormalizeString::execute($beer->name);
            $beer->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beers', function (Blueprint $t) {
            $t->dropColumn('normalized_name');
        });
    }
}
