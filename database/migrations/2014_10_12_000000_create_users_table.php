<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('password', 60);
            $table->string('password_hint', 60);
            // For user verification when using the API functions
            $table->string('user_token', 16)->unique()->nullable()->default(null);
            $table->boolean('admin')->default(false);
            $table->rememberToken();
            // Statistics used to run the leader board
            $table->integer('games_played')->unsigned()->default(0);
            $table->integer('vessels_destroyed')->unsigned()->default(0);
            $table->integer('points_scored')->unsigned()->default(0);
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
        Schema::drop('users');
    }
}
