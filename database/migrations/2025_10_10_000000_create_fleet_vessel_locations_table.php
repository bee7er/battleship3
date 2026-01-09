<?php

use App\FleetVesselLocation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateFleetVesselLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleet_vessel_locations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fleet_vessel_id')->unsigned();
            $table->integer('move_id')->unsigned();
            $table->tinyInteger('row')->unsigned();
            $table->tinyInteger('col')->unsigned();
            $table->enum('status', [
                    FleetVesselLocation::FLEET_VESSEL_LOCATION_NORMAL,
                    FleetVesselLocation::FLEET_VESSEL_LOCATION_HIT,
                    FleetVesselLocation::FLEET_VESSEL_LOCATION_DESTROYED]
            );
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
        Schema::drop('fleet_vessel_locations');
    }
}