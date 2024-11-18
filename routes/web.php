<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/search-locations', [LocationController::class, 'search']);

Route::get('/trace-hierarchy', [LocationController::class, 'traceHierarchy']);
Route::get('/get-{level}-options', [LocationController::class, 'getOptions']);
