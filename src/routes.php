<?php

use Anibalealvarezs\Projectbuilder\Controllers\Calculator\PbCalculatorController as Calculator;

Route::get('add/{a}/{b}', [Calculator::class, 'add'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);
Route::get('substract/{a}/{b}', [Calculator::class, 'substract'])->where(['a' => '[-0-9]+', 'b' => '[0-9]+']);

Auth::routes();

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});