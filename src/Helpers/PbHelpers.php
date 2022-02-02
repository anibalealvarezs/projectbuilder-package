<?php

namespace Anibalealvarezs\Projectbuilder\Helpers;

use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Response;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

class PbHelpers
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
     * @param $key
     * @return mixed
     */
    public static function getDefault($key): mixed
    {
        $self = new self();
        return $self->{$key};
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return array
     */
    #[ArrayShape([
        'read' => "array",
        'create' => "\string[][]",
        'update' => "\string[][]",
        'delete' => "\string[][]"
    ])] public static function getMethodsByPermission(): array
    {
        return [
            'read' => [],
            'create' => ['only' => ['create', 'store']],
            'update' => ['only' => ['edit', 'update']],
            'delete' => ['only' => ['destroy']],
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return array
     */
    public static function getModelsLevels(): array
    {
        return ['level', 'parent', 'grandparent', 'child', 'grandchild'];
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
        if (!Auth::check()) {
            return "";
        }

        $user = PbUser::current();
        return $user->getLocale();
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $string
     * @return string
     */
    public static function toPlural($string): string
    {
        return $string . 's';
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $route
     * @return array
     */
    public static function getStubsList($route): array
    {
        return array_map('basename', File::glob($route . DIRECTORY_SEPARATOR . '*.stub'));
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
        $timestamp = date("Y_m_d_His", $offset ? strtotime("+" . $offset . " seconds") : strtotime("now"));
        $phpFile = str_replace(".stub", "", $filename);
        return Collection::make(database_path('migrations' . DIRECTORY_SEPARATOR))
            ->flatMap(function ($path) use ($phpFile) {
                return File::glob($path . '*_' . $phpFile);
            })->push(database_path('migrations' . DIRECTORY_SEPARATOR . $timestamp . '_' . $phpFile))
            ->first();
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return array
     */
    public static function getMigrationsKeyWords(): array
    {
        return ['create', 'add'];
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
            $vars = [
                'vendor' => self::getDefault('vendor'),
                'package' => self::getDefault('package'),
                'prefix' => self::getDefault('prefix'),
            ];

            foreach ($names as $name) {
                $modelClass = $vars['vendor'] . '\\' . $vars['package'] . '\\Models\\' . $vars['prefix'] . ucfirst($name);
                $controllerClass = $vars['vendor'] . '\\' . $vars['package'] . '\\Controllers\\' . ucfirst($name) . '\\' . $vars['prefix'] . ucfirst($name) . 'Controller';
                switch ($type) {
                    case 'web':
                        Route::resource($name . 's', $controllerClass)->middleware([
                            ...self::getDefaultGroupsMiddlewares()['web'],
                            ...self::getDefaultGroupsMiddlewares()['auth'],
                        ]);
                        Route::group(
                            [
                                'middleware' => [
                                    ...self::getDefaultGroupsMiddlewares()['web'],
                                    ...self::getDefaultGroupsMiddlewares()['auth'],
                                    'role_or_permission:update '. $name . 's'
                                ]
                            ],
                            fn() => self::buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, false, true)
                        );
                        break;
                    default:
                        Route::prefix('api')->group(
                            function() use ($name, $modelClass, $controllerClass) {
                                Route::resource($name . 's', $controllerClass)->middleware([
                                    ...self::getDefaultGroupsMiddlewares()['api'],
                                    ...self::getDefaultGroupsMiddlewares()['auth'],
                                ])->names(self::getApiRoutesNames($name));

                                Route::group(
                                    [
                                        'middleware' => [
                                            ...self::getDefaultGroupsMiddlewares()['api'],
                                            ...self::getDefaultGroupsMiddlewares()['auth'],
                                            'role_or_permission:update '. $name . 's'
                                        ]
                                    ],
                                    fn() => self::buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, true)
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
    public static function buildAdditionalCrudRoutes($name, $modelClass, $controllerClass, bool $api = false, bool $sortable = false)
    {
        Route::prefix($name . 's')->group(
            fn() => Route::prefix('{navigation}')->group(function () use ($name, $modelClass, $controllerClass, $api, $sortable) {
                if (isset($modelClass::$sortable) && $sortable) {
                    Route::match(array('PUT', 'PATCH'), 'sort', [$controllerClass, 'sort'])->name(($api ? 'api.': '') . $name . 's.sort');
                }
                if (isset($modelClass::$enableable)) {
                    Route::match(array('PUT', 'PATCH'), 'enable', [$controllerClass, 'enable'])->name(($api ? 'api.': '') . $name . 's.enable');
                    Route::match(array('PUT', 'PATCH'), 'disable', [$controllerClass, 'disable'])->name(($api ? 'api.': '') . $name . 's.disable');
                }
            })
        );
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
            $route[$method] = 'api.' . $model . 's.' . $method;
        }
        return $route;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $var
     * @return string
     */
    public static function pretty($var): string
    {
        header('Content-Type: application/json; charset=utf-8');
        die(gettype($var) . ' ' . json_encode(
                $var,
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_PRETTY_PRINT |
                JSON_PARTIAL_OUTPUT_ON_ERROR |
                JSON_INVALID_UTF8_SUBSTITUTE
            ));
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param string $value
     * @return string|array
     */
    public static function translateString(string $value): string|array
    {
        if ($json = json_decode($value)) {
            return (
                isset($json->{app()->getLocale()}) && $json->{app()->getLocale()} ?
                    [app()->getLocale() => $json->{app()->getLocale()}] :
                    ["en" => ($json->en ?? '')]
            );
        }
        return $value;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param string $code
     * @return array
     */
    #[ArrayShape(['code' => "string", 'country' => "string"])] public static function getDefaultCountry(string $code): array
    {
        if (file_exists(base_path('/node_modules/flag-icons/country.json'))) {
            $data = json_decode(file_get_contents(base_path('/node_modules/flag-icons/country.json')), true);
            $countries = [];
            foreach ($data as $country) {
                if ($country['iso'] === true) {
                    $countries[$country['code']] = [
                        '1x1' => $country['flag_1x1'],
                        '4x3' => $country['flag_4x3'],
                    ];
                }
            }
        }
        $default = ($countries['gb'] ?? ($countries['es'] ) ?? ['1x1' => "" , '4x3' => ""]);
        return [
            'code' => $code,
            'country' => match ($code) {
                    'es' => ['code' => 'es', 'flags' => (isset($countries['es']) ?? $default)],
                    'fr' => ['code' => 'fr', 'flags' => (isset($countries['fr']) ?? $default)],
                    'de' => ['code' => 'de', 'flags' => (isset($countries['de']) ?? $default)],
                    'it' => ['code' => 'it', 'flags' => (isset($countries['it']) ?? $default)],
                    'pt' => ['code' => 'pt', 'flags' => (isset($countries['pt']) ?? $default)],
                    'ru' => ['code' => 'ru', 'flags' => (isset($countries['ru']) ?? $default)],
                    default => ['code' => 'gb', 'flags' => (isset($countries['gb']) ?? $default)],
                }
        ];
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Request $request
     * @return bool
     */
    public static function isApi(Request $request): bool
    {
        return $request->is('api/*');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return Response
     */
    public static function getWelcomeRoute(): Response
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
    #[ArrayShape(['web' => "string[]", 'api' => "string[]", 'auth' => "string[]"])] public static function getDefaultGroupsMiddlewares(): array
    {
        return [
            'web' => [
                'web',
                'check_https',
                'is_debug_enabled',
                'single_session',
                'set_locale',
            ],
            'api' => [
                'api',
                'can_access_api',
            ],
            'auth' => [
                'auth:sanctum',
                'verified',
            ],
        ];
    }
}
