<?php

use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SerieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//  Endpoints
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/series', [SerieController::class,'index']);
Route::get('/genres', [GenreController::class,'index']);

