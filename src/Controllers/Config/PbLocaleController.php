<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class PbLocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        if (!$language = PbLanguage::findByCode($request->input('locale'))) {
            return Redirect::back();
        }

        if ($request->session()) {
            $request->session()->put('locale', $language->code);
        }
        app()->setLocale($language->code);

        return Redirect::back();
    }
}
