<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

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
    protected $description = 'Project Builde installation';

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
     * @return int
     */
    public function handle()
    {
        // Inertia...
        if ($this->option('inertia')) {
            // $this->installInertia();
            Artisan::call(
                'jetstream:install',
                [
                    'stack' => 'inertia',
                    '--teams' => 'default'
                ]
            );
        }

        shell_exec("composer require anibalealvarezs/projectbuilder-package --no-cache");

        echo "Project Builder installed!\n";
    }

    public function installInertia()
    {
        shell_exec("composer require laravel/jetstream");
    }

    public function publishResources()
    {
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider'
            ]
        );
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider',
                '--tag' => 'migrations'
            ]
        );
        Artisan::call(
            'vendor:publish',
            [
                '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider',
                '--tag' => 'builder-views',
                '--force' => 'default'
            ]
        );
        Artisan::call('cache:clear');
        Artisan::call('migrate');
        Artisan::call(
            'db:seed',
            [
                '--class' => '\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder'
            ]
        );
    }
}
