<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\UserLoginRequest;
use App\Http\Requests\Auth\V1\UserRegisterRequest;
use App\Http\Resources\Auth\V1\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * Register a new user 
     */
    public function register(UserRegisterRequest $request) {
        $validatedData = $request->validated();

        $user = DB::transaction(function () use ($validatedData) {
            $user = User::create($validatedData);

            $user->profile()->create([
                'name' => null,
            ]);

            $user->settings()->create([
                'theme' => 'light',
            ]);

            $user->load(['profile', 'settings']);

            return $user;
        });


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
