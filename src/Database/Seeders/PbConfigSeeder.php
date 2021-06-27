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
        Config::create(['configkey' => '_APP_NAME_', 'value' => 'Builder', 'name' => 'App Name', 'description' => 'App Description']);
        Config::create(['configkey' => '_FORCE_HTTPS_', 'value' => false, 'name' => 'Force HTTPS', 'description' => 'Force HTTPS']);
    }
}
