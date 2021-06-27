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
        Permission::create(['guard_name' => 'admin', 'name' => 'crud super-admin']);
        Permission::create(['guard_name' => 'admin', 'name' => 'create users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'read users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'update users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'delete users']);
        Permission::create(['guard_name' => 'admin', 'name' => 'admin roles permissions']);
        Permission::create(['guard_name' => 'admin', 'name' => 'manage app']);
        Permission::create(['guard_name' => 'admin', 'name' => 'login']);

        // create roles and assign created permissions

        Role::create(['guard_name' => 'admin', 'name' => 'user'])
            ->givePermissionTo(['login']);
        Role::create(['guard_name' => 'admin', 'name' => 'admin'])
            ->givePermissionTo(['create users', 'read users', 'update users', 'delete users', 'manage app', 'login']);
        Role::create(['guard_name' => 'admin', 'name' => 'super-admin'])
            ->givePermissionTo(Permission::all());
    }
}
