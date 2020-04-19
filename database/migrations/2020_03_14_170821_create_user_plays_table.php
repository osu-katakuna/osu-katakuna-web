<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plays', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("beatmapset_id")->unsigned();
            $table->bigInteger("user_id")->unsigned();
            $table->bigInteger("count300");
            $table->bigInteger("count100");
            $table->bigInteger("count50");
            $table->bigInteger("countGeki");
            $table->bigInteger("countKatu");
            $table->bigInteger("miss");
            $table->bigInteger("score");
            $table->bigInteger("maxCombo");
            $table->boolean("fc");
            $table->boolean("pass");
            $table->text("archivedLetter");
            $table->bigInteger("mods")->unsigned();
            $table->tinyInteger("gameMode");
            $table->integer("state");
            $table->text("replay_file")->nullable()->default(null);
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("beatmapset_id")->references("id")->on("beatmap_sets");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_plays');
    }
}
