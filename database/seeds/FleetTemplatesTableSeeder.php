<?php

use Illuminate\Database\Seeder;
use App\FleetTemplate;
use App\Vessel;
use Illuminate\Support\Facades\DB;

class FleetTemplatesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('fleet_templates')->delete();

        $aircraftCarrier = Vessel::where('name', Vessel::VESSEL_TYPE_AIRCRAFT_CARRIER)->firstOrFail();
        $battleship = Vessel::where('name', Vessel::VESSEL_TYPE_BATTLESHIP)->firstOrFail();
        $cruiser = Vessel::where('name', Vessel::VESSEL_TYPE_CRUISER)->firstOrFail();
        $submarine = Vessel::where('name', Vessel::VESSEL_TYPE_SUBMARINE)->firstOrFail();
        $destroyer = Vessel::where('name', Vessel::VESSEL_TYPE_DESTROYER)->firstOrFail();

        $fleetTemplate = new FleetTemplate();
        $fleetTemplate->vessel_id = $aircraftCarrier->id;
        $fleetTemplate->save();

        $fleetTemplate = new FleetTemplate();
        $fleetTemplate->vessel_id = $battleship->id;
        $fleetTemplate->save();


        $fleetTemplate = new FleetTemplate();
        $fleetTemplate->vessel_id = $cruiser->id;
        $fleetTemplate->save();

        $fleetTemplate = new FleetTemplate();
        $fleetTemplate->vessel_id = $submarine->id;
        $fleetTemplate->save();


        $fleetTemplate = new FleetTemplate();
        $fleetTemplate->vessel_id = $destroyer->id;
        $fleetTemplate->save();
    }

}
