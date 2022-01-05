<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait PbControllerTrait
{
    protected function globalInertiaShare()
    {
        return Shares::list([
            'api_data',
            'navigations',
            'locale',
            'debug_status',
        ]);
    }

    protected function getAllowed($permissions)
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
     * @return void
     */
    protected function validateRequest($validationRules, Request $request)
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

    protected function redirectResponse(
        $request,
        $flashMessage,
        $route = 'back',
        $destiny = false,
        $flashStyle = 'danger',
        $withInput = true
    ) {
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

    protected function redirectResponseCRUDSuccess($request, $flashMessage)
    {
        return $this->redirectResponse(
            $request,
            $flashMessage,
            'route',
            $this->names . '.index',
            'success',
            false
        );
    }

    protected function redirectResponseCRUDFail($request, $flashMessage)
    {
        return $this->redirectResponse(
            $request,
            $flashMessage
        );
    }
}
