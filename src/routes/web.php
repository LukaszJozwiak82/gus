<?php

use Illuminate\Support\Facades\Route;
use Ljozwiak\Gus\Http\Controllers\GusController;

Route::group(['middleware' => ['web']], function () {
    Route::get('/gus', [GusController::class, 'index'])->name('gus.index');
    Route::post('/gus/check', [GusController::class, 'searchGus'])->name('gus.search');
});
