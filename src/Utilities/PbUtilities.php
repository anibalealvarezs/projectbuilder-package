<?php

namespace Anibalealvarezs\Projectbuilder\Utilities;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

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
                if (class_exists($this->vendor . '\\' . $this->package . '\\Models\\' . $this->prefix . ucfirst($name))) {
                    $modelClass = $this->vendor . '\\' . $this->package . '\\Models\\' . $this->prefix . ucfirst($name);
                    $controllerClass = $this->vendor . '\\' . $this->package . '\\Controllers\\' . ucfirst($name) . '\\' . $this->prefix . ucfirst($name) . 'Controller';
                } elseif (class_exists('App\\Models\\' . ucfirst($name))) {
                    $modelClass = 'App\\Models\\' . ucfirst($name);
                    $controllerClass = 'App\\Http\\Controllers\\' . ucfirst($name) . 'Controller';
                }
                switch ($type) {
                    case 'web':
                        Route::resource(Str::plural($name), $controllerClass)->middleware([
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
                                    'role_or_permission:update '. Str::plural($name)
                                ]
                            ],
                            fn() => $this->buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, false, true)
                        );
                        break;
                    default:
                        Route::prefix('api')->group(
                            function() use ($name, $modelClass, $controllerClass) {
                                Route::resource(Str::plural($name), $controllerClass)->middleware([
                                    ...getDefaultGroupsMiddlewares('api'),
                                    ...getDefaultGroupsMiddlewares('auth'),
                                ])->names(getApiRoutesNames($name));

                                Route::group(
                                    [
                                        'middleware' => [
                                            ...getDefaultGroupsMiddlewares('api'),
                                            ...getDefaultGroupsMiddlewares('auth'),
                                            'role_or_permission:update '. Str::plural($name)
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
        Route::prefix(Str::plural($name))->group(
            function() use ($name, $modelClass, $controllerClass, $api, $sortable) {
                Route::get('page/{page?}/{perpage?}/{orderby?}/{field?}/{order?}', [$controllerClass, 'index'])->where([
                    'page' => '[0-9]+',
                    'perpage' => '[0-9]+',
                    'orderby' => 'order',
                    'field' => '[a-zA-Z0-9_]+',
                    'order' => 'asc|desc',
                ])->name(($api ? 'api.' : '').Str::plural($name) . '.index.paginated');
                Route::prefix('{navigation}')->group(function () use ($name, $modelClass, $controllerClass, $api, $sortable) {
                    if (isset($modelClass::$sortable) && $sortable) {
                        Route::match(array('PUT', 'PATCH'), 'sort', [$controllerClass, 'sort'])->name(($api ? 'api.': '') . Str::plural($name) . '.sort');
                    }
                    if (isset($modelClass::$enableable)) {
                        Route::match(array('PUT', 'PATCH'), 'enable', [$controllerClass, 'enable'])->name(($api ? 'api.': '') . Str::plural($name) . '.enable');
                        Route::match(array('PUT', 'PATCH'), 'disable', [$controllerClass, 'disable'])->name(($api ? 'api.': '') . Str::plural($name) . '.disable');
                    }
                });
            }
        );
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param string $dir
     * @param string $name
     * @param string $extension
     * @param string $hashName
     * @param string $allowed
     * @return string
     */
    public static function checkName(string $dir, string $name = "", string $extension = "", string $hashName = "", string $allowed = ""): string
    {
        $explodedName = explode('.', $name);
        $explodedName = array_filter($explodedName);
        if (count($explodedName) > 1 && end($explodedName) === $extension) {
            array_pop($explodedName);
        } elseif (empty($explodedName)) {
            if ($hashName) {
                $explodedName = [$hashName];
            } else {
                $explodedName = [Hash::make(date('Y_m_d_His'))];
            }
        }
        $name = Str::slug(implode("-", $explodedName)).'.'.$extension;
        while (Storage::disk('public')->exists($dir . DIRECTORY_SEPARATOR . $name) && $allowed !== $name) {
            $name = self::checkName($dir, renameFile($name));
        }
        return $name;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $name
     * @return mixed
     */
    public static function getFileExtension($name): string
    {
        $explodedName = explode('.', $name);
        return end($explodedName);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $file
     * @param $dir
     * @param $name
     * @return File
     */
    public static function saveFile($file, $dir, $name): File
    {
        return $file->move(Storage::disk('public')->path($dir), $name);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $dir
     * @param $name
     * @param $newName
     * @return mixed
     */
    public static function updateFile($dir, $name, $newName): bool
    {
        return Storage::disk('public')->move($dir . DIRECTORY_SEPARATOR . $name, $dir . DIRECTORY_SEPARATOR . $newName);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $dir
     * @param $name
     * @return mixed
     */
    public static function deleteFile($dir, $name): bool
    {
        if (Storage::disk('public')->exists($dir . DIRECTORY_SEPARATOR . $name)) {
            return Storage::disk('public')->delete($dir . DIRECTORY_SEPARATOR . $name);
        }

        return true;
    }
}
