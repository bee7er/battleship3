<?php

use Illuminate\Database\Seeder;
use App\FleetVesselLocation;
use App\FleetVessel;
use App\Fleet;
use App\Vessel;
use Illuminate\Support\Facades\DB;

class FleetVesselLocationsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('fleet_vessel_locations')->delete();
    }
}
