<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\UserLoginRequest;
use App\Http\Requests\Auth\V1\UserRegisterRequest;
use App\Http\Resources\Auth\V1\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * Gets the current user
     */
    public function user(Request $request) {
        return new UserResource($request->user());
    }

    /**
     * Register a new user 
     */
    public function register(UserRegisterRequest $request) {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        $currentTimestamp = Carbon::now();
        $token = $user->createToken("$request->name\\_$currentTimestamp");

        return new UserResource($user, $token->plainTextToken);
    }
    
    /**
     * Log in the given user
     */
    public function login(UserLoginRequest $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ],
            ];
        }

        $currentTimestamp = Carbon::now();
        $token = $user->createToken("$request->name\\_$currentTimestamp");

        return new UserResource($user, $token->plainTextToken);
    }

    /**
     * Log out from the current session
     */
    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out.',
        ];
    }
}
