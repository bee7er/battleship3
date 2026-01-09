<?php

use Illuminate\Database\Seeder;
use App\Game;
use App\User;
use Illuminate\Support\Facades\DB;

class GamesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('games')->delete();
    }

}
