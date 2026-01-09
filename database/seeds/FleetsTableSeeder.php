<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Fleet;
use App\Game;
use Illuminate\Support\Facades\DB;

class FleetsTableSeeder extends Seeder {

    public function run()
    {
        DB::table('fleets')->delete();
    }

}
