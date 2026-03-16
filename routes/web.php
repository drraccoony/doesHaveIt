<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\OgImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index']);
Route::post('/', [GameController::class, 'check']);
Route::get('/game/{appId}', [GameController::class, 'result'])->where('appId', '[0-9]+')->name('game');
Route::get('/autocomplete', [GameController::class, 'autocomplete']);
Route::get('/recent', [GameController::class, 'recent']);
Route::get('/og-image', [OgImageController::class, 'generate']);
Route::get('/premium', fn () => view('premium'));
