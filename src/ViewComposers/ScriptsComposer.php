<?php
namespace Anibalealvarezs\Projectbuilder\ViewComposers;

use Illuminate\Contracts\View\View;

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
            [ 'comment' => 'jQuery', 'src' => '../../plugins/jquery/jquery.min.js' ],
            [ 'comment' => 'Bootstrap 4', 'src' => '../../plugins/bootstrap/js/bootstrap.bundle.min.js' ],
            [ 'comment' => 'AdminLTE App', 'src' => '../../dist/js/adminlte.min.js' ]
        ];

        $view->with(['scripts' => $scripts]);
    }

}