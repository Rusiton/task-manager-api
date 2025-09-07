<?php

use App\Http\Controllers\Api\V1\BoardController;
use App\Http\Controllers\Api\V1\ColumnController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Auth\V1\AuthenticationController;
use App\Http\Controllers\Auth\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::get('boards/invitations', [BoardController::class, 'getUserInvitations']);
    Route::get('boards/invitations/{invitationToken}', [BoardController::class, 'showInvitation']);
    Route::get('boards/{boardToken}/invitations', [BoardController::class, 'getBoardInvitations']);
    
    Route::post('boards/{boardToken}/invitations', [BoardController::class, 'inviteUser']);
    Route::post('boards/invitations/{invitationToken}/accept', [BoardController::class, 'acceptInvitation']);
    Route::post('boards/invitations/{invitationToken}/decline', [BoardController::class, 'declineInvitation']);

    Route::delete('boards/{boardToken}/invitations/{invitationToken}', [BoardController::class, 'cancelInvitation']);
    Route::delete('boards/{boardToken}/leave', [BoardController::class, 'leaveBoard']);

    Route::apiResource('boards', BoardController::class);
    Route::apiResource('columns', ColumnController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('comments', CommentController::class);
});

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Auth\V1'], function () {

    Route::apiResource('users', UserController::class);
    
    Route::put('/users/profile', [UserController::class, 'updateProfile']);
    Route::put('/users/settings', [UserController::class, 'updateSettings']);

    Route::patch('/users/profile', [UserController::class, 'updateProfile']);
    Route::patch('/users/settings', [UserController::class, 'updateSettings']);

    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
