<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\FleetVessel;

class CreateFleetVesselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleet_vessels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('fleet_id')->unsigned();
            $table->integer('vessel_id')->unsigned();
            $table->enum('status', [
                FleetVessel::FLEET_VESSEL_AVAILABLE,
                FleetVessel::FLEET_VESSEL_STARTED,
                FleetVessel::FLEET_VESSEL_PLOTTED,
                FleetVessel::FLEET_VESSEL_HIT,
                FleetVessel::FLEET_VESSEL_DESTROYED]
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
        Schema::drop('fleet_vessels');
    }
}