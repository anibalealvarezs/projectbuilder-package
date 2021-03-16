<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Anibalealvarezs\Projectbuilder\Models\PbUser as User;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

//Enables us to output flash messaging
use Session;

class PbUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // anibalealvarezs
        $user = new User();
        $user->password = Hash::make('NoEntiendo2321', Config::get('hashing.'.Config::get('hashing.driver')));
        $user->email = 'anibalealvarezs@gmail.com';
        $user->name = 'Aníbal Álvarez';
        $user->current_team_id = 1;
        $user->save();
        // SuperAdmin
        $user->assignRole('super-admin');

        // client
        $user = new User();
        $user->password = Hash::make('Client321', Config::get('hashing.'.Config::get('hashing.driver')));
        $user->email = 'Admin@admin';
        $user->name = 'Admin';
        $user->current_team_id = 2;
        $user->save();
        // Admin
        $user->assignRole('admin');
    }
}
