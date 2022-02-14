<?php

namespace Anibalealvarezs\Projectbuilder\Utilities;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class PbUtilities
{
    public string $vendor;
    public string $package;
    public string $directory;
    public string $prefix;
    public string $name;
    public array $modulekeys;
    /* Configurable */
    public string $storageDirName;
    protected array $toExtract;
    protected array $defaults;
    protected string $configFileName;
    /* End Configurable */

    function __construct(
        $configFileName = "",
        $storageDirName = "",
        $defaults = []
    )
    {
        $this->configFileName = $configFileName ?: 'pbuilder.php';
        $this->storageDirName = $storageDirName ?: 'pbstorage';
        $this->defaults = $defaults ?: require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $this->configFileName);
        $this->toExtract = ['vendor', 'package', 'directory', 'prefix', 'modulekeys'];
        $overrided = [];
        if (file_exists(config_path($this->configFileName))) {
            $overrided = require(config_path($this->configFileName));
        }
        foreach ($this->toExtract as $var) {
            $this->{$var} = $overrided[$var] ?? $this->defaults[$var];
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $type
     * @return void
     */
    public function buildCrudRoutes($type): void
    {
        if (Schema::hasTable('modules')) {
            $names = PbModule::whereIn('modulekey', $this->modulekeys)->pluck('modulekey');

            foreach ($names as $name) {
                $modelClass = $this->vendor . '\\' . $this->package . '\\Models\\' . $this->prefix . ucfirst($name);
                $controllerClass = $this->vendor . '\\' . $this->package . '\\Controllers\\' . ucfirst($name) . '\\' . $this->prefix . ucfirst($name) . 'Controller';
                switch ($type) {
                    case 'web':
                        Route::resource($name . 's', $controllerClass)->middleware([
                            ...getDefaultGroupsMiddlewares('web'),
                            ...getDefaultGroupsMiddlewares('auth'),
                            ...getDefaultGroupsMiddlewares('debug'),
                        ]);
                        Route::group(
                            [
                                'middleware' => [
                                    ...getDefaultGroupsMiddlewares('web'),
                                    ...getDefaultGroupsMiddlewares('auth'),
                                    ...getDefaultGroupsMiddlewares('debug'),
                                    'role_or_permission:update '. $name . 's'
                                ]
                            ],
                            fn() => $this->buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, false, true)
                        );
                        break;
                    default:
                        Route::prefix('api')->group(
                            function() use ($name, $modelClass, $controllerClass) {
                                Route::resource($name . 's', $controllerClass)->middleware([
                                    ...getDefaultGroupsMiddlewares('api'),
                                    ...getDefaultGroupsMiddlewares('auth'),
                                ])->names(getApiRoutesNames($name));

                                Route::group(
                                    [
                                        'middleware' => [
                                            ...getDefaultGroupsMiddlewares('api'),
                                            ...getDefaultGroupsMiddlewares('auth'),
                                            'role_or_permission:update '. $name . 's'
                                        ]
                                    ],
                                    fn() => $this->buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, true)
                                );
                            }
                        );
                        break;
                }
            }
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $name
     * @param $modelClass
     * @param $controllerClass
     * @param bool $api
     * @param bool $sortable
     * @return void
     */
    protected function buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, bool $api = false, bool $sortable = false)
    {
        Route::prefix($name . 's')->group(
            function() use ($name, $modelClass, $controllerClass, $api, $sortable) {
                Route::get('page/{page?}/{perpage?}/{orderby?}/{field?}/{order?}', [$controllerClass, 'index'])->where([
                    'page' => '[0-9]+',
                    'perpage' => '[0-9]+',
                    'orderby' => 'order',
                    'field' => '[a-zA-Z0-9_]+',
                    'order' => 'asc|desc',
                ])->name(($api ? 'api.' : '').$name . 's.index.paginated');
                Route::prefix('{navigation}')->group(function () use ($name, $modelClass, $controllerClass, $api, $sortable) {
                    if (isset($modelClass::$sortable) && $sortable) {
                        Route::match(array('PUT', 'PATCH'), 'sort', [$controllerClass, 'sort'])->name(($api ? 'api.': '') . $name . 's.sort');
                    }
                    if (isset($modelClass::$enableable)) {
                        Route::match(array('PUT', 'PATCH'), 'enable', [$controllerClass, 'enable'])->name(($api ? 'api.': '') . $name . 's.enable');
                        Route::match(array('PUT', 'PATCH'), 'disable', [$controllerClass, 'disable'])->name(($api ? 'api.': '') . $name . 's.disable');
                    }
                });
            }
        );
    }
}
