<?php

use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Inertia\Response;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Foundation\Application;

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @return array
 */
function modelsLevels(): array
{
    return ['level', 'parent', 'grandparent', 'child', 'grandchild'];
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @param $route
 * @return array
 */
function getStubsList($route): array
{
    return array_map('basename', File::glob($route . DIRECTORY_SEPARATOR . '*.stub'));
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @return array
 */
function migrationsKeywords(): array
{
    return ['create', 'add'];
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @param $model
 * @return array
 */
#[Pure] function getApiRoutesNames($model): array
{
    $route = [];
    foreach (getRoutesNames() as $method) {
        $route[$method] = 'api.' . $model . 's.' . $method;
    }
    return $route;
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @return array
 */
function getRoutesNames(): array
{
    return ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'];
}

/**
 * Scope a query to only include popular users.
 *
 * @param string $code
 * @return array
 */
#[ArrayShape(['code' => "string", 'country' => "string"])] function getDefaultCountry(string $code): array
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
 * @return array
 */
#[ArrayShape(['code' => "string", 'country' => "string"])] function getDefaultCountryFromCurrentLocale(): array
{
    return getDefaultCountry(app()->getLocale());
}

/**
 * Scope a query to only include popular users.
 *
 * @param $value
 * @return array
 */
#[ArrayShape(['web' => "string[]", 'api' => "string[]", 'auth' => "string[]", 'debug' => "string[]"])] function getDefaultGroupsMiddlewares($value): array
{
    return match($value) {
        'web' => ['web', 'set_config_data', 'check_https', 'single_session', 'set_locale'],
        'api' => ['api', 'set_config_data', 'check_https', 'can_access_api'],
        'auth' => ['auth:sanctum', 'verified', 'set_current_user'],
        'debug' => ['is_debug_enabled'],
        default => [],
    };
}

/**
 * Scope a query to only include popular users.
 *
 * @param Request $request
 * @return bool
 */
function isApi(Request $request): bool
{
    return $request->is('api/*');
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @param $var
 * @return string
 */
#[NoReturn] function prettyDie($var): string
{
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(
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
function translateString(string $value): string|array
{
    if ($json = json_decode($value)) {
        return (
            isset($json->{app()->getLocale()}) && $json->{app()->getLocale()} ?
                [app()->getLocale() => $json->{app()->getLocale()}] :
                [config('app.locale') => ($json->{config('app.locale')} ?? '')]
        );
    }
    return $value;
}

/**
 * Returns existing migration file if found, else uses the current timestamp.
 *
 * @param $filename
 * @param $offset
 * @return string
 */
function getMigrationFileName($filename, $offset): string
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
#[ArrayShape([
    'read' => "array",
    'create' => "\string[][]",
    'update' => "\string[][]",
    'delete' => "\string[][]"
])] function methodsByPermission(): array
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
 * @param $collection
 * @param array $fields
 * @param string $outputFormatType
 * @param string $outputFormat
 * @return object
 */
function setCollectionAttributeDatetimeFormat(
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
function getCurrentLocale(): string
{
    if (!Auth::check()) {
        return "";
    }

    return app(PbCurrentUser::class)->getLocale();
}

/**
 * Scope a query to only include popular users.
 *
 * @return Response
 */
function welcomeRoute(): Response
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
 * @return Response
 */
function getConfigValue($key): mixed
{
    if (!isApi(request())) {
        return session($key);
    }

    return PbConfig::findByKey($key)->configvalue;
}
