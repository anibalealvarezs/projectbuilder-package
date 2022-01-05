<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Anibalealvarezs\Projectbuilder\Models\PbUser as User;

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
        // SuperAdmin
        User::updateOrCreate(['email' => 'superadmin@superadmin'], ['password' => '123456', 'name' => 'Super Admin', 'current_team_id' => 1])->assignRole('super-admin');

        // Admin
        User::updateOrCreate(['email' => 'admin@admin'], ['password' => '123456', 'name' => 'Admin', 'current_team_id' => 2])->assignRole('admin');
    }
}
