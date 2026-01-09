<?php

use Illuminate\Database\Seeder;
use App\FleetTemplate;
use App\FleetVessel;
use App\Fleet;
use App\Vessel;
use Illuminate\Support\Facades\DB;

class FleetVesselsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('fleet_vessels')->delete();
    }
}
