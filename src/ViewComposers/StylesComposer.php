<?php
namespace Anibalealvarezs\Projectbuilder\ViewComposers;

use Illuminate\Contracts\View\View;

class StylesComposer {
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $styles = [
            //
        ];

        $view->with(['styles' => $styles]);
    }

}
