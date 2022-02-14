<?php

namespace Anibalealvarezs\Projectbuilder\Overrides\Providers;

use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\JetstreamServiceProvider;

class PbJetstreamServiceProvider extends JetstreamServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Jetstream::$registersRoutes) {
            Route::group([
                'namespace' => 'Laravel\Jetstream\Http\Controllers',
                'domain' => config('jetstream.domain'),
                'prefix' => config('jetstream.prefix', config('jetstream.path')),
            ], function () {
                $this->loadRoutesFrom(base_path().'/vendor/laravel/jetstream/routes/'.config('jetstream.stack').'.php');
                $this->loadRoutesFrom(__DIR__ . '/../Routes/inertia.php');
                $this->loadRoutesFrom(__DIR__ . '/../Routes/livewire.php');
            });
        }
    }

    /**
     * Boot any Inertia related services.
     *
     * @return void
     */
    protected function bootInertia()
    {
        parent::bootInertia();

        $dir = resolve(PbUtilities::class)->package.'/Auth/';

        /* $kernel = $this->app->make(Kernel::class);

        $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);
        $kernel->appendToMiddlewarePriority(ShareInertiaData::class);

        if (class_exists(HandleInertiaRequests::class)) {
            $kernel->appendToMiddlewarePriority(HandleInertiaRequests::class);
        } */

        Fortify::loginView(
            fn() => Inertia::render($dir.'Login', [
                'canResetPassword' => Route::has('password.request') && !PbConfig::getValueByKey('_DISABLE_PASSWORD_RESET_'),
                'status' => session('status'),
                'loginEnabled' => !PbConfig::getValueByKey('_DISABLE_LOGIN_'),
            ])
        );

        /* Fortify::requestPasswordResetLinkView(
            fn() => Inertia::render($dir.'ForgotPassword', [
                'status' => session('status'),
            ])
        );

        Fortify::resetPasswordView(
            fn(Request $request) => Inertia::render($dir.'ResetPassword', [
                'email' => $request->input('email'),
                'token' => $request->route('token'),
            ])
        ); */

        Fortify::registerView(
            fn() => Inertia::render($dir.'Register', [
                'registerEnabled' => !PbConfig::getValueByKey('_DISABLE_REGISTER_'),
            ])
        );

        /* Fortify::verifyEmailView(
            fn() => Inertia::render($dir.'VerifyEmail', [
                'status' => session('status'),
            ])
        );

        Fortify::twoFactorChallengeView(
            fn() => Inertia::render($dir.'TwoFactorChallenge')
        );

        Fortify::confirmPasswordView(
            fn() => Inertia::render($dir.'ConfirmPassword')
        ); */
    }
}
