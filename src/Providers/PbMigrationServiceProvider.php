<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Support\ServiceProvider;

class PbMigrationServiceProvider extends ServiceProvider
{
    private $migrationPath;
    private $schemaPath;

    public function __construct($app)
    {
        $this->migrationPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Database/Migrations';
        $this->schemaPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Database/Schema';

        parent::__construct($app);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Migrations
            $stubFiles = PbHelpers::getStubsList($this->migrationPath);
            $keyWords = PbHelpers::getMigrationsKeyWords();
            $offset = 0;
            foreach ($keyWords as $value) {
                foreach ($stubFiles as $ks => $sf) {
                    if (str_starts_with($sf, $value)) {
                        $this->publishes([
                            $this->migrationPath . DIRECTORY_SEPARATOR . $sf => PbHelpers::getMigrationFileName($sf,
                                $offset)
                        ], 'migrations');
                        unset($stubFiles[$ks]);
                    }
                }
                $offset++;
            }
            foreach ($stubFiles as $sf) {
                $this->publishes([
                    $this->migrationPath . DIRECTORY_SEPARATOR . $sf => PbHelpers::getMigrationFileName($sf, $offset)
                ], 'migrations');
            }
            // Schema dump
            $this->publishes([
                $this->schemaPath . DIRECTORY_SEPARATOR . 'mysql-schema.dump' => database_path('schema/' . 'mysql-schema.dump'),
            ], 'schema');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
