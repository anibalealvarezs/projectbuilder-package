<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbConfig as Config;
use Illuminate\Database\Seeder;

class PbConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default Config
        Config::updateOrCreate(['configkey' => '_APP_NAME_'], ['configvalue' => 'Builder', 'name' => 'App Name', 'description' => 'App Description']);
        Config::updateOrCreate(['configkey' => '_FORCE_HTTPS_'], ['configvalue' => false, 'name' => 'Force HTTPS', 'description' => 'Force HTTPS']);
    }
}
