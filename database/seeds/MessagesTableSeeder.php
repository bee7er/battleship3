<?php

use App\Game;
use Illuminate\Database\Seeder;
use App\Message;
use App\MessageText;
use App\User;
use Illuminate\Support\Facades\DB;

class MessagesTableSeeder extends Seeder {

    public function run()
    {
        DB::table('messages')->delete();
    }
}
