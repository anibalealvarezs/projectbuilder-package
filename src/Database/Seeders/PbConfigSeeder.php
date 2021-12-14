<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Anibalealvarezs\Projectbuilder\Models\PbConfig as Config;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
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
        $moduleConfig = PbModule::where('modulekey', 'config')->first();
        // Default Config
        Config::updateOrCreate(['configkey' => '_APP_NAME_'], ['configvalue' => 'Builder', 'name' => 'App Name', 'description' => 'App Description', 'module_id' => $moduleConfig->id]);
        Config::updateOrCreate(['configkey' => '_FORCE_HTTPS_'], ['configvalue' => false, 'name' => 'Force HTTPS', 'description' => 'Force HTTPS', 'module_id' => $moduleConfig->id]);
        Config::updateOrCreate(['configkey' => '_SAVE_LOGS_'], ['configvalue' => false, 'name' => 'Save Logs', 'description' => 'Save Logs', 'module_id' => $moduleConfig->id]);
        Config::updateOrCreate(['configkey' => '_API_ENABLED_'], ['configvalue' => true, 'name' => 'API Enabled', 'description' => 'API Enabled', 'module_id' => null]);
    }
}
