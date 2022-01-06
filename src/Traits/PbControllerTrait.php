<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;

trait PbControllerTrait
{
    /**
     * Remove the specified resource from storage.
     *
     * @return array
     */
    protected function globalInertiaShare(): array
    {
        return Shares::list([
            'api_data',
            'navigations',
            'locale',
            'debug_status',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $permissions
     * @return array
     */
    protected function getAllowed($permissions): array
    {
        $allowed = [];
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $allowed[$permission] = PbUser::current()->hasPermissionTo($permission);
            }
        } else {
            $allowed[$permissions] = PbUser::current()->hasPermissionTo($permissions);
        }

        return $allowed;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $validationRules
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|null
     */
    protected function validateRequest($validationRules, Request $request): Redirector|RedirectResponse|Application|null
    {
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }

            return $this->redirectResponse($request, $current);
        }

        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $request
     * @param $flashMessage
     * @param string $route
     * @param bool $destiny
     * @param string $flashStyle
     * @param bool $withInput
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponse(
        $request,
        $flashMessage,
        string $route = 'back',
        string $destiny = "",
        string $flashStyle = 'danger',
        bool $withInput = true
    ): RedirectResponse|Application|Redirector {
        $request->session()->flash('flash.banner', $flashMessage);
        $request->session()->flash('flash.bannerStyle', $flashStyle);

        $redirect = redirect();

        switch ($route) {
            case 'route':
                if (is_array($destiny)) {
                    $redirect = $redirect->route($destiny['route'], $destiny['id']);
                } else {
                    $redirect = $redirect->route($destiny);
                }
                break;
            case 'back':
                $redirect = $redirect->back();
                break;
            default:
                break;
        }
        if ($withInput) {
            $redirect = $redirect->withInput();
        }
        return $redirect;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $request
     * @param $flashMessage
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponseCRUDSuccess($request, $flashMessage): Redirector|Application|RedirectResponse
    {
        return $this->redirectResponse(
            $request,
            $flashMessage,
            'route',
            $this->controllerVars->level->names . '.index',
            'success',
            false
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $request
     * @param $flashMessage
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponseCRUDFail($request, $flashMessage): Redirector|Application|RedirectResponse
    {
        return $this->redirectResponse(
            $request,
            $flashMessage
        );
    }
}
