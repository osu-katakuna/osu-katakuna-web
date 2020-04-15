<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeatmapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beatmaps', function (Blueprint $table) {
            $table->id();
            $table->date("sync_date");
            $table->string("title");
            $table->string("title_unicode")->nullable();
            $table->string("artist");
            $table->string("artist_unicode")->nullable();
            $table->integer("status");
            $table->bigInteger("creator_id")->unsigned();
            $table->string("creator");
            $table->longText("tags");
            $table->string("source");
            $table->bigInteger("genre_id")->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beatmaps');
    }
}
