<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
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
        if ($user = PbUser::role('super-admin')->first()) {
            Team::query()->delete();
            Team::upsert([
                ['name' => 'SuperAdmin', 'personal_team' => true, 'user_id' => $user->id],
                ['name' => 'Admin', 'personal_team' => true, 'user_id' => $user->id],
                ['name' => 'User', 'personal_team' => true, 'user_id' => $user->id],
            ], ['name'], ['personal_team', 'user_id']);
            if ($superAdminTeam = Team::where('name', 'SuperAdmin')->first()) {
                $superAdmins = PbUser::role('super-admin')->get();
                foreach ($superAdmins as $superAdmin) {
                    $superAdmin->current_team_id = $superAdminTeam->id;
                    $superAdmin->save();
                }
            }
            if ($adminTeam = Team::where('name', 'Admin')->first()) {
                $admins = PbUser::role('admin')->get();
                foreach ($admins as $admin) {
                    $admin->current_team_id = $adminTeam->id;
                    $admin->save();
                }
            }
        }
    }
}
