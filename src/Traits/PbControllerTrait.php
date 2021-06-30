<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\Shares;
use Anibalealvarezs\Projectbuilder\Models\PbUser;

use Auth;

trait PbControllerTrait
{

    protected function globalInertiaShare()
    {
        return Shares::list([
            'navigations',
            'locale',
        ]);
    }

    protected function getAllowed($permissions)
    {
        $allowed = [];
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $allowed[$permission] = PbUser::find(Auth::user()->id)->hasPermissionTo($permission);
            }
        } else {
            $allowed[$permissions] = PbUser::find(Auth::user()->id)->hasPermissionTo($permissions);
        }

        return $allowed;
    }

    protected function validationCheck($validator, $request)
    {
        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }

            $this->redirectResponse($request, $current);
        }
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
                $redirect = $redirect->route($destiny);
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
            $this->name . '.index',
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
