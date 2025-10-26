<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('invitationToken', function ($token) {
            return \App\Models\BoardUserInvitation::with(['board', 'invitedBy'])
                ->where('token', $token)
                ->firstOrFail();
        });

        Route::bind('boardToken', function ($token) {
            return \App\Models\Board::where('token', $token)
                ->firstOrFail();
        });

        Route::bind('userToken', function ($token) {
            return \App\Models\User::where('token', $token)
                ->firstOrFail();
        });

        JsonResource::withoutWrapping();
    }
}
