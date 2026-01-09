<?php

use Illuminate\Database\Seeder;
use App\Vessel;
use Illuminate\Support\Facades\DB;

class VesselsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('vessels')->delete();

        $vessel = new Vessel();
        $vessel->name = Vessel::VESSEL_TYPE_AIRCRAFT_CARRIER;
        $vessel->length = 5;
        $vessel->points = 7;
        $vessel->save();

        $vessel = new Vessel();
        $vessel->name = Vessel::VESSEL_TYPE_BATTLESHIP;
        $vessel->length = 4;
        $vessel->points = 6;
        $vessel->save();

        $vessel = new Vessel();
        $vessel->name = Vessel::VESSEL_TYPE_CRUISER;
        $vessel->length = 3;
        $vessel->points = 5;
        $vessel->save();

        $vessel = new Vessel();
        $vessel->name = Vessel::VESSEL_TYPE_SUBMARINE;
        $vessel->length = 3;
        $vessel->points = 5;
        $vessel->save();


        $vessel = new Vessel();
        $vessel->name = Vessel::VESSEL_TYPE_DESTROYER;
        $vessel->length = 2;
        $vessel->points = 4;
        $vessel->save();

    }

}
