<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Jetstream;

use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Anibalealvarezs\Projectbuilder\Traits\PbInstallTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Anibalealvarezs\Projectbuilder\Helpers\PbInertiaManager;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

class PbUserProfileController extends UserProfileController
{
    use PbInstallTrait;

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
            array_merge(
                $this->globalInertiaShare(),
            )
        );

        return self::inertia()->render($request, 'Show', [
            'sessions' => $this->sessions($request)->all(),
            'roles' => PbUser::find(Auth::user()->id)->roles,
        ]);
    }

    /**
     * @return PbInertiaManager
     */
    protected static function inertia(): PbInertiaManager
    {
        if (is_null(static::$inertiaManager)) {
            static::$inertiaManager = new PbInertiaManager();
        }

        return static::$inertiaManager;
    }
}
