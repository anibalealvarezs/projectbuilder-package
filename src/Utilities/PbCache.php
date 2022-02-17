<?php

namespace Anibalealvarezs\Projectbuilder\Utilities;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;

class PbCache
{
    /**
     * Scope a query to only include popular users.
     *
     * @param string $package
     * @param string $type
     * @param string|null $class
     * @param string|null $function
     * @param string|null $model
     * @param string|null $modelFunction
     * @param int $modelId
     * @param array $pagination
     * @param bool $byRoles
     * @param bool $byUser
     * @return string
     */
    public static function buildCacheIndex(
        string $package,
        string $type,
        string $class = null,
        string $function = null,
        string $model = null,
        string $modelFunction = null,
        int $modelId = 0,
        array $pagination = [],
        bool $byRoles = false,
        bool $byUser = false
    ): string
    {
        $index = $package . ':' . $type;
        if ($class) {
            $index .= ':' . $class;
        }
        if ($function) {
            $index .= ':' . $function;
        }
        if ($model) {
            $index .= ':model:' . $model . ':' . ($modelFunction ?? 'none') . ':' . $modelId;
        }
        if ($pagination) {
            $index .=
                ':page:' . ($pagination['page'] ?? 0) .
                ':perpage:' . ($pagination['perpage'] ?? 0) .
                ':orderby:' . ($pagination['orderby'] ?? 'null') .
                ':field:' . ($pagination['field'] ?? 'null') .
                ':order:' . ($pagination['order'] ?? 'null');
        }
        if ($byRoles) {
            $index .= ':roles:' . app(PbCurrentUser::class)->roles->pluck('name')->implode('_');
        } elseif ($byUser) {
            $index .= ':user:' . Auth::id();
        }
        return $index;
    }

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
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    #[ArrayShape(['data' => "mixed", 'index' => "null|string"])]
    public static function run(
        Closure $closure,
        string $package,
        string $type = 'controller',
        string $class = null,
        string $function = 'index',
        string $model = null,
        string $modelFunction = null,
        int $modelId = 0,
        array $pagination = [],
        bool $byRoles = false,
        bool $byUser = false,
        bool $toArray = null
    ): array
    {
        $index = self::buildCacheIndex(
            package: $package,
            type: $type,
            class: Str::lower(getClassName($class)),
            function: Str::lower(getFunctionName($class, $function)),
            model: $model,
            modelFunction: $modelFunction,
            modelId: $modelId,
            pagination: $pagination,
            byRoles: $byRoles,
            byUser: $byUser
        );
        $indexedCache = false;

        // if (false) {
        if (getConfigValue('_CACHE_ENABLED_') && Cache::store('redis')->has($index)) {
            $indexedCache = true;
            $data = unserialize(Cache::store('redis')->get($index));
        }

        $result = $data ?? self::processClosure($closure, $index);

        return ['data' => $result && $toArray ? $result->toArray() : $result, 'index' => ($indexedCache ? $index : null)];
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param Closure $closure
     * @param string $index
     * @return array|object|null
     * @throws InvalidArgumentException
     */
    public static function processClosure(Closure $closure, string $index): array|object|null
    {
        $data = $closure();
        if (getConfigValue('_CACHE_ENABLED_')) {
            Cache::store('redis')->set($index, serialize($data));
        }
        return $data;
    }
}
