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
            [ 'comment' => 'Google Font: Source Sans Pro', 'href' => 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback' ],
            [ 'comment' => 'Font Awesome', 'href' => asset('pbstorage/plugins/fontawesome-free/css/all.min.css') ],
            [ 'comment' => 'icheck bootstrap', 'href' => asset('pbstorage/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ],
            [ 'comment' => 'Theme style', 'href' => asset('pbstorage/css/adminlte.min.css') ]
        ];

        $view->with(['styles' => $styles]);
    }

}