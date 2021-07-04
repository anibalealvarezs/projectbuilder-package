<?php

namespace Anibalealvarezs\Projectbuilder\Database\Seeders;

use Illuminate\Database\Seeder;

class PbMainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permissions Seeder
        $this->call([
            PbLanguagesSeeder::class,
            PbCountriesSeeder::class,
            PbCitiesSeeder::class,
            PbSpatieSeeder::class,
            PbUsersSeeder::class,
            PbTeamSeeder::class,
            PbNavigationSeeder::class,
            PbConfigSeeder::class,
            PbLoggerSeeder::class,
        ]);
    }
}
