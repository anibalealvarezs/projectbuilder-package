<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbModule as Module;
use Illuminate\Database\Seeder;

class PbModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::updateOrCreate(['modulekey' => 'user'], ['name' => 'Users', 'status' => 1]);
        Module::updateOrCreate(['modulekey' => 'config'], ['name' => 'Config', 'status' => 1]);
        Module::updateOrCreate(['modulekey' => 'navigation'], ['name' => 'Config', 'status' => 1]);
        Module::updateOrCreate(['modulekey' => 'permission'], ['name' => 'Config', 'status' => 1]);
        Module::updateOrCreate(['modulekey' => 'role'], ['name' => 'Config', 'status' => 1]);
    }
}
