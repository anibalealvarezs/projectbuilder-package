<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbNavigation as Navigation;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
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
        // Get Permissions
        $loginPermission = PbPermission::where('name', 'login')->first();
        $readUsersPermission = PbPermission::where('name', 'read users')->first();
        $readRolesPermission = PbPermission::where('name', 'read roles')->first();
        $readPermissionsPermission = PbPermission::where('name', 'read permissions')->first();
        $readConfigsPermission = PbPermission::where('name', 'read configs')->first();
        $readNavigationsPermission = PbPermission::where('name', 'read navigations')->first();
        $readLoggersPermission = PbPermission::where('name', 'read loggers')->first();
        $moduleUser = PbModule::where('modulekey', 'user')->first();
        $moduleConfig = PbModule::where('modulekey', 'config')->first();
        $moduleNavigation = PbModule::where('modulekey', 'navigation')->first();
        $moduleRole = PbModule::where('modulekey', 'role')->first();
        $modulePermission = PbModule::where('modulekey', 'permission')->first();

        // Parents
        Navigation::updateOrCreate(['destiny' => 'dashboard', 'module_id' => null], ['name' => 'Dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => $loginPermission->id, 'position' => 0]);
        $usersParent = Navigation::updateOrCreate(['destiny' => '#navigation-users-roles', 'module_id' => $moduleUser->id], ['name' => 'Users & Roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readUsersPermission->id, 'position' => 1]);
        $settingsParent = Navigation::updateOrCreate(['destiny' => '#navigation-settings', 'module_id' => null], ['name' => 'Settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readConfigsPermission->id, 'position' => 2]);
        Navigation::updateOrCreate(['name' => 'Logger', 'module_id' => null], ['destiny' => 'loggers.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readLoggersPermission->id, 'position' => 2]);

        // Children
        if ($usersParent) {
            Navigation::updateOrCreate(['destiny' => 'users.index', 'module_id' => $moduleUser->id], ['name' => 'Users', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'position' => 0]);
            Navigation::updateOrCreate(['destiny' => 'roles.index', 'module_id' => $moduleRole->id], ['name' => 'Roles', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readRolesPermission->id, 'position' => 1]);
            Navigation::updateOrCreate(['destiny' => 'permissions.index', 'module_id' => $modulePermission->id], ['name' => 'Permissions', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readPermissionsPermission->id, 'position' => 2]);
        }
        if ($settingsParent) {
            Navigation::updateOrCreate(['destiny' => 'configs.index', 'module_id' => $moduleConfig->id], ['name' => 'Config', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readConfigsPermission->id, 'position' => 0]);
            Navigation::updateOrCreate(['destiny' => 'navigations.index', 'module_id' => $moduleNavigation->id], ['name' => 'Navigations', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readNavigationsPermission->id, 'position' => 1]);
        }
    }
}
