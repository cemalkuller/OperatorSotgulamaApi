<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OperatorLookupController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/operator-lookup', [OperatorLookupController::class, 'lookup']);
