<?php

Route::get('welcome', function(){
    echo 'Hello from the calculator package!';
});

Route::get('add/{a}/{b}', 'Anibalealvarezs\ProjectBuilder\ProjectBuilderController@add');
Route::get('substract/{a}/{b}', 'Anibalealvarezs\ProjectBuilder\ProjectBuilderController@subtract');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});