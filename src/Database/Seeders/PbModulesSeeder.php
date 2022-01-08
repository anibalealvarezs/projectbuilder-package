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
            ['modulekey' => 'user', 'name' => json_encode(['en' => 'Users']), 'status' => 1],
            ['modulekey' => 'config', 'name' => json_encode(['en' => 'Configs']), 'status' => 1],
            ['modulekey' => 'navigation', 'name' => json_encode(['en' => 'Navigations']), 'status' => 1],
            ['modulekey' => 'permission', 'name' => json_encode(['en' => 'Permission']), 'status' => 1],
            ['modulekey' => 'role', 'name' => json_encode(['en' => 'Roles']), 'status' => 1],
        ], ['modulekey'], ['name', 'status']);
    }
}
