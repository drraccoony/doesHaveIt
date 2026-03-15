<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/docs', [SwaggerController::class, 'ui']);
Route::get('/docs/spec', [SwaggerController::class, 'spec']);

Route::middleware('throttle:api')->group(function () {
    Route::get('/check/appid/{appId}', [ApiController::class, 'checkByAppId'])
        ->where('appId', '[0-9]+');

    Route::get('/check/search/{term}', [ApiController::class, 'checkByTerm'])
        ->where('term', '.+');
});
