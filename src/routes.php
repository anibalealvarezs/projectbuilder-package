<?php

use Anibalealvarezs\ProjectBuilder\ProjectBuilderController as ProjectBuilder;

Route::get('welcome', function(){
    echo 'Hello from the calculator package!';
});

Route::get('add/{a}/{b}', [ProjectBuilder::class, 'add']);
Route::get('substract/{a}/{b}', [ProjectBuilder::class, 'substract']);

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});