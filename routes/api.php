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
    Route::get('users/search/{searchParam}', [UserController::class, 'search']);

    Route::get('boards/invitations', [BoardController::class, 'getUserInvitations']);
    Route::get('boards/invitations/{invitationToken}', [BoardController::class, 'showInvitation']);
    Route::get('boards/{boardToken}/invitations', [BoardController::class, 'getBoardInvitations']);

    Route::put('tasks/moveInsideColumn', [TaskController::class, 'moveInsideColumn']);
    Route::put('tasks/moveToColumn', [TaskController::class, 'moveToColumn']);
    Route::put('tasks/moveToEmptyColumn', [TaskController::class, 'moveToEmptyColumn']);
    Route::put('boards/{boardToken}/members/{userToken}/setRole', [BoardController::class, 'setRole']);
    
    Route::post('boards/{boardToken}/invitations', [BoardController::class, 'inviteUser']);
    Route::post('boards/invitations/{invitationToken}/accept', [BoardController::class, 'acceptInvitation']);
    Route::post('boards/invitations/{invitationToken}/decline', [BoardController::class, 'declineInvitation']);

    Route::delete('boards/{boardToken}/invitations/{invitationToken}', [BoardController::class, 'cancelInvitation']);
    Route::delete('boards/{boardToken}/user/leave', [BoardController::class, 'leaveBoard']);
    Route::delete('boards/{boardToken}/members/{userToken}/kick', [BoardController::class, 'kickUser']);

    Route::apiResource('boards', BoardController::class);
    Route::apiResource('columns', ColumnController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('comments', CommentController::class);
});

Route::group(['prefix' => 'v1/auth', 'namespace' => 'App\Http\Controllers\Auth\V1'], function () {

    Route::apiResource('users', UserController::class)->except(['update']);

    Route::patch('/users', [UserController::class, 'update']);

    Route::get('/users/{user}/boards', [UserController::class, 'getBoards']);
    
    Route::patch('/users/profile', [UserController::class, 'updateProfile']);
    Route::patch('/users/settings', [UserController::class, 'updateSettings']);

    Route::patch('/users/profile', [UserController::class, 'updateProfile']);
    Route::patch('/users/settings', [UserController::class, 'updateSettings']);

    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
