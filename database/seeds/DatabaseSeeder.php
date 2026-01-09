<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Reference tables
        $this->call(UsersTableSeeder::class);
        $this->call(VesselsTableSeeder::class);
        $this->call(FleetTemplatesTableSeeder::class);
        $this->call(MessageTextsTableSeeder::class);
        // Test tables
        $this->call(GamesTableSeeder::class);
        $this->call(FleetsTableSeeder::class);
        $this->call(FleetVesselsTableSeeder::class);
        $this->call(FleetVesselLocationsTableSeeder::class);
        $this->call(MessagesTableSeeder::class);

        Model::reguard();
    }
}
