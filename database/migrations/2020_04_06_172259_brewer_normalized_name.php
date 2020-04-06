<?php

use App\Brewer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BrewerNormalizedName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brewers', function(Blueprint $t){
            $t->string('normalized_name')->nullable();
        });

        $string_normalizer = new \Larabeers\Utils\NormalizeString();
        foreach(Brewer::all() as $brewer) {
            $brewer->normalized_name = $string_normalizer->execute($brewer->name);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brewers', function(Blueprint $t) {
           $t->dropColumn('normalized_name');
        });
    }
}
