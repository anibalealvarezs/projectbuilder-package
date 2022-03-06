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
            ['modulekey' => 'user', 'name' => json_encode(['en' => 'Users', 'es' => 'Usuarios']), 'status' => 1],
            ['modulekey' => 'config', 'name' => json_encode(['en' => 'Configs', 'es' => 'Configuraciones']), 'status' => 1],
            ['modulekey' => 'navigation', 'name' => json_encode(['en' => 'Navigations', 'es' => 'Navegación']), 'status' => 1],
            ['modulekey' => 'permission', 'name' => json_encode(['en' => 'Permissions', 'es' => 'Permisos']), 'status' => 1],
            ['modulekey' => 'role', 'name' => json_encode(['en' => 'Roles', 'es' => 'Roles']), 'status' => 1],
            ['modulekey' => 'logger', 'name' => json_encode(['en' => 'Logger', 'es' => 'Logger']), 'status' => 1],
            ['modulekey' => 'file', 'name' => json_encode(['en' => 'Files Manager', 'es' => 'Administrador de Archivos']), 'status' => 1],
        ], ['modulekey'], ['name', 'status']);
    }
}
