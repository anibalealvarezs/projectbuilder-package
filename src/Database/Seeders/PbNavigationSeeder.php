<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbNavigation as Navigation;
use Illuminate\Database\Seeder;

class PbNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default Config
        Navigation::create(['name' => 'Dashboard', 'destiny' => 'dashboard', 'type' => 'route', 'parent' => 0]);
        Navigation::create(['name' => 'Users', 'destiny' => 'users.index', 'type' => 'route', 'parent' => 0, 'module' => 'pbuser']);
        Navigation::create(['name' => 'Config', 'destiny' => 'configs.index', 'type' => 'route', 'parent' => 0, 'module' => 'pbconfig']);
    }
}
