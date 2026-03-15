<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\OgImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index']);
Route::get('/autocomplete', [GameController::class, 'autocomplete']);
Route::get('/recent', [GameController::class, 'recent']);
Route::get('/og-image', [OgImageController::class, 'generate']);
