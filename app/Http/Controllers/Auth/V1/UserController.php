<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\UpdateUserProfileRequest;
use App\Http\Requests\Auth\V1\UpdateUserRequest;
use App\Http\Requests\Auth\V1\UpdateUserSettingsRequest;
use App\Http\Resources\Auth\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }


    public function index(Request $request){
        return new UserResource($request->user());
    }




    public function show(User $user) {
        return new UserResource($user);
    }




    public function update(UpdateUserRequest $request) {
        $validated = $request->validated();

        $user = $request->user();
        $user->update($validated);

        return new UserResource($user);
    }



    public function updateProfile(UpdateUserProfileRequest $request) {
        $validated = $request->validated();

        $user = $request->user();
        $user->profile()->update($validated);

        return new UserResource($user);
    }



    public function updateSettings(UpdateUserSettingsRequest $request) {
        $validated = $request->validated();

        $user = $request->user();
        $user->settings()->update($validated);

        return new UserResource($user);
    }



    public function destroy(Request $request) {
        $request->user()->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
