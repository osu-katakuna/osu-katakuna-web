<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeatmapSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beatmap_sets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("beatmap_id")->unsigned();
            $table->string("name");
            $table->integer("mode");
            $table->integer("hp");
            $table->integer("cs");
            $table->integer("od");
            $table->integer("ar");
            $table->float("bpm");
            $table->integer("length");
            $table->float("stars");
            $table->string("md5");
            $table->integer("status");
            $table->string("author");
            $table->float("aim");
            $table->float("speed");
            $table->string("filename")->nullable();

            $table->foreign("beatmap_id")->references("id")->on("beatmaps");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beatmap_sets');
    }
}
