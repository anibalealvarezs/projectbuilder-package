<?php

$models = [
    /* object => Directory*/
    'user' => 'User',
    'config' => 'Config',
    'navigation' => 'Navigation',
    'role' => 'Permission',
    'permission' => 'Permission',
    'logger' => 'Logger'
];

foreach($models as $model => $directory) {
    Route::prefix('api')->group(function () use ($model, $directory) {
        Route::prefix($model.'s')->middleware(['auth:sanctum', 'api_access'])->group(function () use ($model, $directory) {
            $class = '\\Anibalealvarezs\\Projectbuilder\\Controllers\\'.$directory.'\\Pb'.ucfirst($model).'Controller';
            Route::get('/', [$class, 'index']);
            Route::post('/', [$class, 'store']);
            Route::get('{'.$model.'}', [$class, 'show']);
            Route::match(['put', 'patch'], '{'.$model.'}', [$class, 'update']);
            Route::delete('{'.$model.'}', [$class, 'destroy']);
        });
    });
}
