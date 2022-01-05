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
        Module::upsert([
            ['modulekey' => 'user', 'name' => 'Users', 'status' => 1],
            ['modulekey' => 'config', 'name' => 'Config', 'status' => 1],
            ['modulekey' => 'navigation', 'name' => 'Config', 'status' => 1],
            ['modulekey' => 'permission', 'name' => 'Config', 'status' => 1],
            ['modulekey' => 'role', 'name' => 'Config', 'status' => 1],
        ], ['modulekey'], ['name', 'status']);
    }
}
