<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFriendshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friendships', function (Blueprint $table) {
            $table->bigInteger("user")->unsigned();
            $table->bigInteger("friend")->unsigned();
            $table->timestamps();

            $table->foreign("user")->references("id")->on("users");
            $table->foreign("friend")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_friendships');
    }
}
