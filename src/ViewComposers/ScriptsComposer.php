<?php
namespace Anibalealvarezs\Projectbuilder\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class ScriptsComposer {
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $scripts = [
            //
        ];

        $view->with(['scripts' => $scripts]);
    }

}
