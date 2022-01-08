<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class PbHelpers
{
    public const PB_VENDOR = 'Anibalealvarezs';
    public const PB_PACKAGE = 'Projectbuilder';
    public const PB_DIR = 'projectbuilder-package';
    public const PB_PREFIX = 'Pb';
    public const PB_NAME = 'builder';
    public const NON_EXISTENT_MODULES = [
        'logger'
    ];

    function __construct()
    {
        // TODO: Implement __construct() method.
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $collection
     * @param array $fields
     * @param string $outputFormatType
     * @param string $outputFormat
     * @return object
     */
    public static function setCollectionAttributeDatetimeFormat(
        $collection,
        array $fields = [],
        string $outputFormatType = 'method',
        string $outputFormat = "toDateTimeString"
    ): object {
        return $collection->map(function ($array) use ($fields, $outputFormat, $outputFormatType) {
            foreach ($fields as $f) {
                $array[$f] = (
                $outputFormatType == "method" ?
                    Carbon::parse($array[$f])->{$outputFormat}() :
                    Carbon::parse($array[$f])->format($outputFormat)
                );
            }
            return $array;
        });
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    public static function getCustomLocale(): string
    {
        if (Auth::check()) {
            $user = PbUser::current();
            return $user->getLocale();
        }
        return "";
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $string
     * @return string
     */
    public static function toPlural($string): string
    {
        return $string.'s';
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $route
     * @return array
     */
    public static function getStubsList($route): array
    {
        return array_map('basename', File::glob($route.DIRECTORY_SEPARATOR.'*.stub'));
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $filename
     * @param $offset
     * @return string
     */
    public static function getMigrationFileName($filename, $offset): string
    {
        $timestamp = date("Y_m_d_His", $offset ? strtotime("+".$offset." seconds") : strtotime("now"));
        $phpFile = str_replace(".stub", "", $filename);
        return Collection::make(database_path('migrations'.DIRECTORY_SEPARATOR))
            ->flatMap(function ($path) use ($phpFile) {
                return File::glob($path.'*_'.$phpFile);
            })->push(database_path('migrations'.DIRECTORY_SEPARATOR.$timestamp.'_'.$phpFile))
            ->first();
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return array
     */
    public static function getMigrationsKeyWords(): array
    {
        return [
            'create', 'add'
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return bool
     */
    public static function getDebugStatus(): bool
    {
        return (bool) PbConfig::getValueByKey('_DEBUG_MODE_');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $key
     * @param $helper
     * @param $prefix
     * @param $vendor
     * @param $package
     * @return object
     */
    public static function buildControllerVars($key, $helper, $prefix, $vendor, $package): object
    {
        $object = (object) [];
        $object->key = $key;
        $object->keys = $helper::toPlural($key);
        $object->model = $prefix . $key;
        $object->models = $helper::toPlural($object->model);
        $object->name = strtolower($key);
        $object->names = $helper::toPlural($object->name);
        $object->prefixName = strtolower($prefix . $key);
        $object->prefixNames = $helper::toPlural($object->prefixName);
        $object->modelPath = $vendor . "\\" . $package . "\\Models\\" . $object->model;
        $object->viewsPath = $package . "/" . $object->keys . "/";
        $object->table = (new $object->modelPath())->getTable();
        return $object;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $type
     * @return void
     */
    public static function buildCrudRoutes($type): void
    {
        $models = [...self::NON_EXISTENT_MODULES, ...PbModule::pluck('modulekey')];

        foreach ($models as $model) {
            $controller = self::PB_VENDOR . '\\' . self::PB_PACKAGE . '\\Controllers\\'.ucfirst($model).'\\' . self::PB_PREFIX .ucfirst($model).'Controller';
            switch ($type) {
                case 'web':
                    Route::resource($model.'s', $controller)->middleware(['web', 'auth:sanctum', 'verified']);
                    break;
                default:
                    Route::prefix('api')->group(
                        fn () => Route::resource($model.'s', $controller)->middleware(['auth:sanctum', 'verified', 'api_access'])->names(self::getApiRoutesNames($model))
                    );
                    break;
            }
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return array
     */
    public static function getApiRoutesNames($model): array
    {
        $route = [];
        foreach (['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'] as $method) {
            $route[$method] = 'api.'.$model.'s.'.$method;
        }
        return $route;
    }
}
