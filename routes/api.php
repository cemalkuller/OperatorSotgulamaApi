<?php
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LimitController;
use App\Http\Controllers\Api\OperatorLookupController;

Route::middleware('auth:sanctum')->post('/operator-lookup', [OperatorLookupController::class, 'lookup']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user/limits', [LimitController::class, 'limits']);
Route::get('/app/version', [AppVersionController::class, 'index']);

