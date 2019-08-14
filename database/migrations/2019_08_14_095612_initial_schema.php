<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beers', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('type');
            $t->timestamps();
        });

        Schema::create('brewers', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('country');
            $t->string('city');
            $t->timestamps();
        });

        Schema::create('beer_brewer', function (Blueprint $t) {
            $t->integer('beer_id');
            $t->integer('brewer_id');
        });

        Schema::create('labels', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->integer('beer_id');
            $t->year('year')->nullable();
            $t->integer('month')->nullable();
            $t->integer('album');
            $t->integer('page');
            $t->integer('position');
        });

        Schema::create('tags', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('text');
        });

        Schema::create('label_tag', function (Blueprint $t) {
            $t->integer('label_id');
            $t->integer('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
