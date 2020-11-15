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
        Permission::create(['name' => 'crud super-admin']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'read users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // create roles and assign created permissions

        // this can be done as separate statements
        Role::create(['name' => 'admin'])
            ->givePermissionTo(['create users', 'read users', 'update users', 'delete users']);

        Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());
    }
}
