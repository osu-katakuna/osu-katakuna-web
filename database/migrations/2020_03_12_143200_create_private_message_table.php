<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_message', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("from_user_id")->unsigned();
            $table->bigInteger("to_user_id")->unsigned();
            $table->string("message");
            $table->boolean("seen");
            $table->timestamps();

            $table->foreign("from_user_id")->references('id')->on('users')->onDelete("cascade");
            $table->foreign("to_user_id")->references('id')->on('users')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_message');
    }
}
