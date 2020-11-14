<?php

use Anibalealvarezs\Projectbuilder\ProjectbuilderController as Projectbuilder;

Route::get('welcome', function(){
    echo 'Hello from the calculator package!';
});

Route::get('add/{a}/{b}', [Projectbuilder::class, 'add'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);
Route::get('substract/{a}/{b}', [Projectbuilder::class, 'substract'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});