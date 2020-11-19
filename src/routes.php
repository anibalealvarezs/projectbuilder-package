<?php

use Anibalealvarezs\Projectbuilder\Controllers\PbCalculatorController as Calculator;

Route::namespace('Auth')->group(function () {
    Route::get('/login','LoginController@show_login_form')->name('login');
    Route::post('/login','LoginController@process_login')->name('login');
    Route::get('/register','LoginController@show_signup_form')->name('register');
    Route::post('/register','LoginController@process_signup');
    Route::post('/logout','LoginController@logout')->name('logout');
});

Route::get('add/{a}/{b}', [Calculator::class, 'add'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);
Route::get('substract/{a}/{b}', [Calculator::class, 'substract'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});