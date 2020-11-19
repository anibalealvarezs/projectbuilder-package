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
            [ 'comment' => 'jQuery', 'src' => asset('pbstorage/plugins/jquery/jquery.min.js') ],
            [ 'comment' => 'Bootstrap 4', 'src' => asset('pbstorage/plugins/bootstrap/js/bootstrap.bundle.min.js') ],
            [ 'comment' => 'AdminLTE App', 'src' => asset('pbstorage/js/adminlte.min.js') ]
        ];

        $view->with(['scripts' => $scripts]);
    }

}