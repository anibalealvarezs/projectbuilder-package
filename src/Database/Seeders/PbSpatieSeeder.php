<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name' => 'admin', 'name' => 'crud super-admin', 'alias' => 'CRUD Super Admin']);
        Permission::create(['guard_name' => 'admin', 'name' => 'create users', 'alias' => 'Create Users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'read users', 'alias' => 'Read Users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'update users', 'alias' => 'Update Users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'delete users', 'alias' => 'Delete Users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'admin roles permissions', 'alias' => 'Admin Roles & Permissions']);
        Permission::create(['guard_name' => 'admin', 'name' => 'manage app', 'alias' => 'Manage App']);
        Permission::create(['guard_name' => 'admin', 'name' => 'login', 'alias' => 'Login']);

        // create roles and assign created permissions

        Role::create(['guard_name' => 'admin', 'name' => 'user', 'alias' => 'User'])
            ->givePermissionTo(['login']);
        Role::create(['guard_name' => 'admin', 'name' => 'admin', 'alias' => 'Admin'])
            ->givePermissionTo(['create users', 'read users', 'update users', 'delete users', 'manage app', 'login']);
        Role::create(['guard_name' => 'admin', 'name' => 'super-admin', 'alias' => 'Super Admin'])
            ->givePermissionTo(Permission::all());
    }
}
