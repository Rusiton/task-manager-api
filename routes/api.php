<?php

use App\Http\Controllers\Api\V1\BoardController;
use App\Http\Controllers\Api\V1\ColumnController;
use App\Http\Controllers\Auth\V1\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::apiResource('boards', BoardController::class);
    Route::apiResource('columns', ColumnController::class);
});

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Auth\V1'], function () {

    Route::get('/user', [AuthenticationController::class, 'user'])->middleware('auth:sanctum');

    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
