<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PbSpatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // updateOrCreate permissions
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'crud super-admin'], ['alias' => 'CRUD Super Admin']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'admin roles permissions'], ['alias' => 'Admin Roles & Permissions']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'manage app'], ['alias' => 'Manage App']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'login'], ['alias' => 'Login']);
        // CRUD Users
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create users'], ['alias' => 'Create Users']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read users'], ['alias' => 'Read Users']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update users'], ['alias' => 'Update Users']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete users'], ['alias' => 'Delete Users']);
        // CRUD Configs
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create configs'], ['alias' => 'Create Configs']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read configs'], ['alias' => 'Read Configs']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update configs'], ['alias' => 'Update Configs']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete configs'], ['alias' => 'Delete Configs']);
        // CRUD Navigations
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create navigations'], ['alias' => 'Create Navigations']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read navigations'], ['alias' => 'Read Navigations']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update navigations'], ['alias' => 'Update Navigations']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete navigations'], ['alias' => 'Delete Navigations']);
        // CRUD Permissions
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create permissions'], ['alias' => 'Create Permissions']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read permissions'], ['alias' => 'Read Permissions']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update permissions'], ['alias' => 'Update Permissions']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete permissions'], ['alias' => 'Delete Permissions']);
        // CRUD Roles
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'create roles'], ['alias' => 'Create Roles']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'read roles'], ['alias' => 'Read Roles']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'update roles'], ['alias' => 'Update Roles']);
        Permission::updateOrCreate(['guard_name' => 'admin', 'name' => 'delete roles'], ['alias' => 'Delete Roles']);

        // updateOrCreate roles and assign updateOrCreated permissions

        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'user'], ['alias' => 'User'])
            ->givePermissionTo(['login']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'admin'], ['alias' => 'Admin'])
            ->givePermissionTo(['create users', 'read users', 'update users', 'delete users', 'manage app', 'login']);
        Role::updateOrCreate(['guard_name' => 'admin', 'name' => 'super-admin'], ['alias' => 'Super Admin'])
            ->givePermissionTo(Permission::all());
    }
}
