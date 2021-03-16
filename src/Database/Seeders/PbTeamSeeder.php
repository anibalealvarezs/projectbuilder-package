<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

//Enables us to output flash messaging
use Session;

class PbTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Super Admin
        $team = new Team();
        $team->name = 'SuperAdmin';
        $team->personal_team = 1;
        $team->user_id = 1;
        $team->save();

        // Admin
        $team = new Team();
        $team->name = 'Admin';
        $team->personal_team = 1;
        $team->user_id = 1;
        $team->save();
    }
}
