<?php

namespace Anibalealvarezs\Projectbuilder\Utilities;

use Closure;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\ArrayShape;

class PbCache
{
    /**
     * Scope a query to only include popular users.
     *
     * @param Closure $closure
     * @param string $package
     * @param string $type
     * @param string|null $class
     * @param string $function
     * @param string|null $model
     * @param string|null $modelFunction
     * @param int $modelId
     * @param array $pagination
     * @param bool $byRoles
     * @param bool $byUser
     * @param bool $toArray
     * @return array
     */
    #[ArrayShape(['data' => "mixed", 'keys' => "null|string", 'tags' => "null|string"])]
    public static function run(
        Closure $closure,
        string $package,
        string $type = 'controller',
        string $class = null,
        string $function = 'index',
        string $model = null,
        int $modelId = 0,
        /***************************/
        string $modelFunction = null,
        array $pagination = [],
        bool $byRoles = false,
        bool $byUser = false,
        bool $toArray = null
    ): array
    {
        $tags = andTag($package, $type, $class, $function, $model, $modelId);
        $keys = andKey($modelFunction, (!$modelId ? $pagination : []), $byRoles, $byUser);
        $stored = false;

        // if (false) {
        if (getConfigValue('_CACHE_ENABLED_') && Cache::store('redis')->tags($tags)->has($keys ?: 'all')) {
            $stored = true;
            $data = Cache::store('redis')->tags($tags)->get($keys ?: 'all');
        }

        $result = $data ?? self::processClosure($closure, $tags, $keys);

        return ['data' => $result && $toArray ? $result->toArray() : $result, 'keys' => ($stored ? $keys : ""), 'tags' => ($stored ? implode(',', $tags) : "")];
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return array
     */
    public static function argsOrder(): array
    {
        return ['closure', 'package', 'type', 'class', 'function', 'model', 'modelId', 'modelFunction', 'pagination', 'byRoles', 'byUser', 'toArray'];
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Closure $closure
     * @param array $tags
     * @param string $keys
     * @return array|object|null
     */
    public static function processClosure(Closure $closure, array $tags, string $keys): array|object|null
    {
        $data = $closure();
        if (getConfigValue('_CACHE_ENABLED_')) {
            Cache::store('redis')->tags($tags)->set($keys ?: 'all', $data);
        }
        return $data;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param string $package
     * @param string $type
     * @param string|null $class
     * @param string $function
     * @param string|null $model
     * @param int $modelId
     * @param string $modelFunction
     * @param array $pagination
     * @param bool $byRoles
     * @param bool $byUser
     * @return bool
     */
    public static function clear(
        string $package,
        string $type = 'controller',
        string $class = null,
        string $function = 'index',
        string $model = null,
        int $modelId = 0,
        /***************************/
        string $modelFunction = "",
        array $pagination = [],
        bool $byRoles = false,
        bool $byUser = false
    ): bool
    {
        $tags = andTag($package, $type, $class, $function, $model, $modelId);
        $keys = andKey($modelFunction, $pagination, $byRoles, $byUser);

        if ($keys) {
            return Cache::store('redis')->tags($tags)->forget($keys);
        } else {
            return Cache::store('redis')->tags($tags)->flush();
        }
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public static function clearAll(): bool
    {
        return Cache::store('redis')->flush();
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param string $package
     * @param string $models
     * @return void
     */
    public static function clearIndex(string $package, string $models)
    {
        self::clear(
            package: $package,
            class: 'builder_controller',
            model: $models,
        );
        self::clear(
            package: $package,
            class: 'model_controller',
            model: $models,
        );
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param string $package
     * @param string $models
     * @param int $modelId
     * @return void
     */
    public static function clearModel(string $package, string $models, int $modelId)
    {
        self::clear(
            package: $package,
            class: 'builder_controller',
            model: $models,
        );
        self::clear(
            package: $package,
            class: 'model_controller',
            model: $models,
        );
        self::clear(
            package: $package,
            class: 'builder_controller',
            function: 'show',
            model: $models,
            modelId: $modelId,
        );
        self::clear(
            package: $package,
            class: 'model_controller',
            function: 'show',
            model: $models,
            modelId: $modelId,
        );
        self::clear(
            package: $package,
            class: 'builder_controller',
            function: 'edit',
            model: $models,
            modelId: $modelId,
        );
        self::clear(
            package: $package,
            class: 'model_controller',
            function: 'edit',
            model: $models,
            modelId: $modelId,
        );
    }
}
