<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Database\Seeder;
use App\Models\Team;

//Enables us to output flash messaging
use Session;

class PbTeamSeeder extends Seeder
{
    public static function byPass()
    {
        //
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PbUsersSeeder::class,
        ]);

        $user = PbUser::role('super-admin')->first();
        if ($user) {
            Team::updateOrCreate(['name' => 'SuperAdmin'], ['personal_team' => true, 'user_id' => $user->id]);
            Team::updateOrCreate(['name' => 'Admin'], ['personal_team' => true, 'user_id' => $user->id]);
            Team::updateOrCreate(['name' => 'User'], ['personal_team' => true, 'user_id' => $user->id]);
        }
    }
}
