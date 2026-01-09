<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Game;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->enum('status', [
                    Game::STATUS_EDIT,
                    Game::STATUS_WAITING,
                    Game::STATUS_READY,
                    Game::STATUS_ENGAGED,
                    Game::STATUS_COMPLETED,
                    Game::STATUS_DELETED,
                    Game::STATUS_UNDELETED,
                ]
            );
            $table->unsignedInteger('player_one_id');
            $table->unsignedInteger('player_two_id')->nullable();
            // For player 2 link
            $table->string('player_two_link_token', 16)->unique()->nullable()->default(null);
            $table->unsignedInteger('winner_id');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
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
        Schema::drop('games');
    }
}