<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use Exception;

class PbCacheController
{
    use PbControllerTrait;

    /**
     * Sort model elements
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function clear(Request $request): RedirectResponse
    {
        if (app(PbCurrentUser::class)->hasPermissionTo('clear cache')) {
            try {
                PbCache::clearAll();
            } catch (Exception $e) {
                return $this->redirectResponse(
                    request: $request,
                    flashMessage: 'Cache could not be cleared: ' . $e->getMessage(),
                    withInput: false,
                );
            }
        } else {
            return $this->redirectResponse(
                request: $request,
                flashMessage: 'You are not authorized to clear the cache',
                withInput: false,
            );
        }

        return $this->redirectResponse(
            request: $request,
            flashMessage: 'Cache cleared successfully',
            flashStyle: 'success',
            withInput: false,
        );
    }

    /**
     * Sort model elements
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function clearLaravel(Request $request): RedirectResponse
    {
        if (app(PbCurrentUser::class)->hasPermissionTo('clear laravel cache')) {
            try {
                Artisan::call('cache:clear');
            } catch (Exception $e) {
                return $this->redirectResponse(
                    request: $request,
                    flashMessage: 'Laravel\'s cache could not be cleared: ' . $e->getMessage(),
                    withInput: false,
                );
            }
        } else {
            return $this->redirectResponse(
                request: $request,
                flashMessage: 'You are not authorized to clear the Laravel\'s cache',
                withInput: false,
            );
        }

        return $this->redirectResponse(
            request: $request,
            flashMessage: 'Laravel\'s cache cleared successfully',
            flashStyle: 'success',
            withInput: false,
        );
    }

}
