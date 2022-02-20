<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Config;

use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Traits\PbControllerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PbLocaleController extends Controller
{
    use PbControllerTrait;

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        if (!$language = PbLanguage::findByCode($request->input('locale'))) {
            return $this->redirectResponse(
                request: $request,
                flashMessage: 'The selected language does not exist.',
                withInput: false,
            );
        }

        if (!$request->session()) {
            return $this->redirectResponse(
                request: $request,
                flashMessage: 'Current session is not available.',
                withInput: false,
            );
        }

        $request->session()->put('locale', $language->code);
        app()->setLocale($language->code);

        return $this->redirectResponse(
            request: $request,
            flashMessage: 'Language updated successfully.',
            flashStyle: 'success',
            withInput: false,
        );
    }
}
