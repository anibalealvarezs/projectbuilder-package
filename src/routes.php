<?php

Route::get('welcome', function(){
    echo 'Hello from the calculator package!';
});

Route::get('add/{a}/{b}', 'Anibalealvarezs\ProjectBuilder\ProjectBuilderController@add');
Route::get('substract/{a}/{b}', 'Anibalealvarezs\ProjectBuilder\ProjectBuilderController@subtract');