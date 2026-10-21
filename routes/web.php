<?php

use App\Http\Controllers\LfmoController;
use Illuminate\Support\Facades\Route;

Route::controller(LfmoController::class)->group(function () {
    Route::get('/', 'index')->name('matches.index');
    Route::get('/matches/create', 'create')->name('matches.create');
    Route::post('/matches', 'store')->name('matches.store');
    Route::get('/matches/{id}', 'show')->whereNumber('id')->name('matches.show');
    Route::post('/matches/{id}/join', 'join')->name('matches.join');
    Route::post('/matches/{id}/leave', 'leave')->name('matches.leave');
    Route::post('/matches/{id}/balance', 'balance')->name('matches.balance');
    Route::post('/matches/{id}/drop', 'drop')->name('matches.drop');
    Route::get('/pitches', 'pitches')->name('pitches.index');
    Route::post('/pitches', 'storePitch')->name('pitches.store');
    Route::get('/profile', 'profile')->name('profile');
});
