<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BrewerAddressLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brewers', function (Blueprint $t) {
            $t->string('address')->after('city')->nullable();
            $t->float('latitude', 9, 7)->after('address')->nullable();
            $t->float('longitude', 9, 7)->after('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brewers', function (Blueprint $t) {
            $t->dropColumn('address');
            $t->dropColumn('latitude');
            $t->dropColumn('longitude');
        });
    }
}
