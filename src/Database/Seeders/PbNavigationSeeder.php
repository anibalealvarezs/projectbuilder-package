<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

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
        $this->call([
            PbTeamSeeder::class,
        ]);

        if (Navigation::count() == 0) {
            // Get Permissions
            $loginPermission = PbPermission::where('name', 'login')->first();
            $readUsersPermission = PbPermission::where('name', 'read users')->first();
            $readRolesPermission = PbPermission::where('name', 'read roles')->first();
            $readPermissionsPermission = PbPermission::where('name', 'read permissions')->first();
            $readConfigsPermission = PbPermission::where('name', 'read configs')->first();
            $readNavigationsPermission = PbPermission::where('name', 'read navigations')->first();

            // Parents
            Navigation::updateOrCreate(['name' => 'Dashboard'], ['destiny' => 'dashboard', 'type' => 'route', 'parent' => 0, 'permission_id' => $loginPermission->id, 'module' => null]);
            $usersParent = Navigation::updateOrCreate(['name' => 'Users & Roles'], ['destiny' => '#navigation-users-roles', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readUsersPermission->id, 'module' => null]);
            $settingsParent = Navigation::updateOrCreate(['name' => 'Settings'], ['destiny' => '#navigation-settings', 'type' => 'custom', 'parent' => 0, 'permission_id' => $readConfigsPermission->id, 'module' => null]);

            // Children
            if ($usersParent) {
                Navigation::updateOrCreate(['name' => 'Users'], ['destiny' => 'users.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readUsersPermission->id, 'module' => 'pbuser']);
                Navigation::updateOrCreate(['name' => 'Roles'], ['destiny' => 'roles.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readRolesPermission->id, 'module' => 'pbroles']);
                Navigation::updateOrCreate(['name' => 'Permissions'], ['destiny' => 'permissions.index', 'type' => 'route', 'parent' => $usersParent->id, 'permission_id' => $readPermissionsPermission->id, 'module' => 'pbpermissions']);
            }
            if ($settingsParent) {
                Navigation::updateOrCreate(['name' => 'Config'], ['destiny' => 'configs.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readConfigsPermission->id, 'module' => 'pbconfig']);
                Navigation::updateOrCreate(['name' => 'Navigations'], ['destiny' => 'navigations.index', 'type' => 'route', 'parent' => $settingsParent->id, 'permission_id' => $readNavigationsPermission->id, 'module' => 'pbnavigations']);
            }
        }
    }
}
