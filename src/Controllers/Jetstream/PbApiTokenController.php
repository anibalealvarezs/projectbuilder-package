<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Jetstream;

use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Laravel\Jetstream\Jetstream;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Anibalealvarezs\Projectbuilder\Utilities\PbInertiaManager;
use Laravel\Jetstream\Http\Controllers\Inertia\ApiTokenController;

class PbApiTokenController extends ApiTokenController
{
    use PbControllerTrait;

    /**
     * The Inertia manager instance.
     *
     * @var PbInertiaManager
     */
    public static $inertiaManager;

    /**
     * Show the user API token screen.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        Inertia::share(
            'shared',
            $this->globalInertiaShare()
        );

        return self::inertia()->render($request, 'API/Index', [
            'tokens' => $request->user()->tokens->map(function ($token) {
                return $token->toArray() + [
                        'last_used_ago' => optional($token->last_used_at)->diffForHumans(),
                    ];
            }),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
        ]);
    }

    /**
     * @return PbInertiaManager
     */
    protected static function inertia(): PbInertiaManager
    {
        if (is_null(static::$inertiaManager)) {
            static::$inertiaManager = app(PbInertiaManager::class);
        }

        return static::$inertiaManager;
    }
}
