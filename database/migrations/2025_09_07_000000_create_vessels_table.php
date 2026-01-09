<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Vessel;

class CreateVesselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->enum('name', [
                Vessel::VESSEL_TYPE_AIRCRAFT_CARRIER,
                Vessel::VESSEL_TYPE_BATTLESHIP,
                Vessel::VESSEL_TYPE_CRUISER,
                Vessel::VESSEL_TYPE_SUBMARINE,
                Vessel::VESSEL_TYPE_DESTROYER
                ]
            );
            $table->integer('length')->unsigned();
            $table->integer('points')->unsigned();
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
        Schema::drop('vessels');
    }
}