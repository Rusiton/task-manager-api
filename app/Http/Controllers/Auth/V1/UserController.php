<?php

namespace App\Http\Controllers\Auth\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\V1\UpdateUserProfileRequest;
use App\Http\Requests\Auth\V1\UpdateUserRequest;
use App\Http\Requests\Auth\V1\UpdateUserSettingsRequest;
use App\Http\Resources\Api\V1\BoardResource;
use App\Http\Resources\Api\V1\UserSearchResource;
use App\Http\Resources\Auth\V1\UserResource;
use App\Models\Board;
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




    public function show($searchParam) {
        $user = User::where('name', $searchParam)->first();

        if (!$user) {
            // Tries to search by token if search by name failed.
            $user = User::where('token', $searchParam)->first();
        }

        return $user 
            ? new UserResource($user)
            : response()->json(['message' => 'The requested instance could not be found.'], 404);
    }



    public function search($searchParam) {
        $users = User
        ::where('email', 'like', "%$searchParam%")
        ->orWhere('name', 'like', "%$searchParam%")
        ->get();


        // Custom parameter to exclude users that are members of some board or have already been invited to that board.
        $excludeFromBoard = request('excludeFromBoard');
        if ($excludeFromBoard) {
            $board = Board::where('token', $excludeFromBoard)->first();

            // Exclude users that are already a member of the board.
            $users = $users->reject(function ($user) use ($excludeFromBoard) {
                return 
                    $user->owned_boards->contains('token', $excludeFromBoard)
                    || 
                    $user->joined_boards->contains('token', $excludeFromBoard);
            });

            // Exclude users that have already been invited to the board.
            $users = $users->reject(function ($user) use ($board) {
                $invitationsToBoard = $user->receivedInvitations->filter(function ($invitation) use ($board) {
                    return $invitation['board_id'] === $board->id;
                });

                return (count($invitationsToBoard) > 0);
            });
        }
        

        return UserSearchResource::collection($users);
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

        return new UserResource($user->load('profile'));
    }



    public function updateSettings(UpdateUserSettingsRequest $request) {
        $validated = $request->validated();

        $user = $request->user();
        $user->settings()->update($validated);

        return new UserResource($user->load('settings'));
    }



    public function destroy(Request $request) {
        $request->user()->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }



    public function getBoards(User $user) {
        [$ownedBoards, $joinedBoards] = [
            $user->owned_boards, 
            $user->joined_boards
        ];

        return [
            'ownedBoards' => BoardResource::collection($ownedBoards),
            'joinedBoards' => BoardResource::collection($joinedBoards)
        ];
    }
}
