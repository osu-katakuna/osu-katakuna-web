<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			      $table->string("username", 20)->collation('ascii_general_ci');
			      $table->string("email");
            $table->string("avatar")->nullable();
			      $table->string("password_hash");
            $table->boolean("bot")->default(false);
            $table->boolean("banned")->default(false);

            $table->softDeletes();
            $table->timestamps();
            $table->index(['username', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
