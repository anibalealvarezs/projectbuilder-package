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
        // Parents
        Navigation::create(['name' => 'Dashboard', 'destiny' => 'dashboard', 'type' => 'route', 'parent' => 0, 'permission' => ['login'], 'nodule' => null]);
        $usersParent = Navigation::create(['name' => 'Users & Roles', 'destiny' => '#navigation-users-roles', 'type' => 'custom', 'parent' => 0, 'permission' => ['read users'], 'nodule' => null]);
        $settingsParent = Navigation::create(['name' => 'Settings', 'destiny' => '#navigation-settings', 'type' => 'custom', 'parent' => 0, 'permission' => ['crud super-admin'], 'nodule' => null]);

        // Children
        if ($usersParent) {
            Navigation::create(['name' => 'Users', 'destiny' => 'users.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission' => ['read users'], 'module' => 'pbuser']);
            Navigation::create(['name' => 'Roles', 'destiny' => 'roles.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission' => ['admin roles permissions'], 'module' => 'pbroles']);
            Navigation::create(['name' => 'Permissions', 'destiny' => 'permissions.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission' => ['admin roles permissions'], 'module' => 'pbpermissions']);
        }
        if ($settingsParent) {
            Navigation::create(['name' => 'Config', 'destiny' => 'configs.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission' => ['crud super-admin'], 'module' => 'pbconfig']);
            Navigation::create(['name' => 'Navigations', 'destiny' => 'navigations.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission' => ['crud super-admin'], 'module' => 'pbnavigations']);
        }
    }
}
