<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PbInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbuilder:install {--inertia : Includes Jetstream and Inertia installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Builder installation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Inertia...
        echo "[[ Process start ]]\n";
        if ($this->option('inertia')) {
            echo "-- [[ Pre-requirements ]]\n";
            $this->installInertia();
        }
        echo "-- [[ Installing Project Builder ]]\n";
        $this->installProjectBuilder();
        echo "[[ Project Builder installed! ]]\n";
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function installInertia()
    {
        echo "---- Requiring Jetstream...\n";
        shell_exec("composer require laravel/jetstream");
        echo "---- Installing Jetstream & Inertia...\n";
        Artisan::call(
            'jetstream:install',
            [
                'stack' => 'inertia',
                '--teams' => 'default'
            ]
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function installProjectBuilder()
    {
        $this->requirePackage();
        $this->publishResources();
        $this->migrateAndSeed();
        $this->createLinks();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function requirePackage()
    {
        echo "---- Requiring Project Builder...\n";
        shell_exec("composer require anibalealvarezs/projectbuilder-package --no-cache");
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function publishResources()
    {
        echo "---- Publishing Spatie's files...\n";
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider'
            ]
        );
        echo "---- Publishing Project Builder's stubs... \n";
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider',
                '--tag' => 'migrations'
            ]
        );
        echo "---- Publishing Project Builder's views... \n";
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider',
                '--tag' => 'builder-views',
                '--force' => 'default'
            ]
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function migrateAndSeed()
    {
        echo "---- Clearing cache... \n";
        Artisan::call('cache:clear');
        echo "---- Migrating... \n";
        Artisan::call('migrate');
        echo "---- Seeding... \n";
        Artisan::call(
            'db:seed',
            [
                '--class' => '\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder'
            ]
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function createLinks()
    {
        echo "---- Creating links\n";
        Artisan::call('storage:link');
    }
}
