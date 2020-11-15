<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Anibalealvarezs\Projectbuilder\Models\PbUser as User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

//Enables us to output flash messaging
use Session;

class PbUsersSeeder extends Seeder
{
    protected $guard_name = 'admin';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // anibalealvarezs
        $user = new User();
        $user->password = Hash::make('NoEntiendo2321');
        $user->email = 'anibalealvarezs@gmail.com';
        $user->name = 'Aníbal Álvarez';
        $user->save();
        // SuperAdmin
        $user->assignRole('super-admin');

        // client
        $user = new User();
        $user->password = Hash::make('Client321');
        $user->email = 'client@client';
        $user->name = 'Client';
        $user->save();
        // Admin
        $user->assignRole('admin');
    }
}
