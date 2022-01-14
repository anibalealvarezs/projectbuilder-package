<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Jetstream;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Anibalealvarezs\Projectbuilder\Helpers\PbInertiaManager;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

class PbUserProfileController extends UserProfileController
{
    use PbControllerTrait;

    /**
     * The Inertia manager instance.
     *
     * @var PbInertiaManager
     */
    public static $inertiaManager;

    /**
     * Show the general profile settings screen.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        Inertia::share(
            'shared',
            [
                ...$this->globalInertiaShare(),
                ...Shares::list(['languages']),
                ...Shares::list(['countries']),
            ]
        );

        return self::inertia()->render($request, 'Show', [
            'sessions' => $this->sessions($request)->all(),
            'roles' => PbUser::current()->roles
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
