<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        $user = new User();
        $user->name = 'System Admin';
        $user->password = Hash::make('battle202');
        $user->password_hint = 'Conflict with double room number';
        $user->user_token = User::getNewToken();
        $user->admin = true;
        $user->save();

        $user = new User();
        $user->name = 'Brian';
        $user->password = Hash::make('battle101');
        $user->password_hint = 'Conflict with single room number';
        $user->user_token = User::getNewToken();
        $user->admin = true;
        $user->save();
    }

}
