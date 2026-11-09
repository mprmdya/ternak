<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\SensorLogController;

Route::post('/sensor-log', [SensorLogController::class, 'store']);
Route::get('/sensor-history', [SensorLogController::class, 'history']);
