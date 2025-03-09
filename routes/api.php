<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\SingerController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

// Genre routes
Route::get('genres', [GenreController::class, 'index']);
Route::get('genres/{id}', [GenreController::class, 'show']);
Route::get('genres/{id}/songs', [GenreController::class, 'songs']);

// Singer routes
Route::get('singers', [SingerController::class, 'index']);
Route::get('singers/{id}', [SingerController::class, 'show']);
Route::get('singers/{id}/songs', [SingerController::class, 'songs']);

// Song routes
Route::get('songs', [SongController::class, 'index']);
Route::get('songs/{id}', [SongController::class, 'show']);
Route::get('songs/{id}/stream', [SongController::class, 'stream'])->name('songs.stream');
Route::get('search', [SongController::class, 'search']);
