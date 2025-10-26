<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InviteUserBoardRequest;
use App\Http\Requests\Api\V1\StoreBoardRequest;
use App\Http\Requests\Api\V1\UpdateBoardRequest;
use App\Http\Resources\Api\V1\BoardResource;
use App\Http\Resources\Api\V1\BoardInvitationResource;
use App\Models\Board;
use App\Models\BoardUserInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BoardController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),    
        ];
    }


    /**
     * 
     * Standard CRUD action
     * 
     */


    public function index(Request $request) {
        [$ownedBoards, $joinedBoards] = [
            $request->user()->owned_boards, 
            $request->user()->joined_boards
        ];

        return [
            'ownedBoards' => BoardResource::collection($ownedBoards),
            'joinedBoards' => BoardResource::collection($joinedBoards)
        ];
    }



    public function show(Board $board) {
        Gate::authorize('show', $board);
        
        return new BoardResource($board);
    }



    public function store(StoreBoardRequest $request) {
        $validated = $request->validated();

        $board = $request->user()->owned_boards()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'owner_id' => $request->user()->id,
        ]);

        return new BoardResource($board);
    }



    public function update(UpdateBoardRequest $request, Board $board) {
        Gate::authorize('modify', $board);

        $validated = $request->validated();
        
        $board->update($validated);

        return new BoardResource($board);
    }



    public function destroy(Board $board) {
        Gate::authorize('modify', $board);

        $board->delete();

        return ['message' => 'Board was deleted successfully'];
    }


    /**
     * 
     * User-Board related actions.
     * 
     */

    /**
     * Invite a user to a board.
     */
    public function inviteUser(Board $board, InviteUserBoardRequest $request) {
        $validated = $request->validated();

        Gate::authorize('manageInvitations', [BoardUserInvitation::class, $board]);

        $invitedUser = User::where('token', $validated['userToken'])->first();

        if(BoardUserInvitation::where('board_id', $board->id)
            ->where('user_id', $invitedUser->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->exists())
            {
                return response()->json([
                    'message' => 'An invitation to this board has already been sent to this user',
                ], 422);
            }
        
        Gate::authorize('invite', [BoardUserInvitation::class, $board]);
        
        $expiredOrDeclinedInvitation = BoardUserInvitation::where('board_id', $board->id)
            ->where('user_id', $invitedUser->id)
            ->where('status', ['declined', 'expired'])
            ->first();

        if ($expiredOrDeclinedInvitation) $expiredOrDeclinedInvitation->delete();

        $request->user()->sentInvitations()->create([
            'board_id' => $board->id,
            'user_id' => $invitedUser->id,
            'invited_by' => $request->user()->id,
            'status' => 'pending',
            'expires_at' => $request['expiresAt'],
            // Token is automatically generated during creation within the BoardUserInvitation model
        ]);

        return response()->json([
            'message' => 'Invitation was sent successfully',
        ]);
    }

    /**
     * Cancel an already sent invitation
     */
    public function cancelInvitation(Board $board, BoardUserInvitation $invitation) {
        Gate::authorize('manageInvitations', [BoardUserInvitation::class, $invitation->board]);

        if ($invitation->board_id !== $board->id) {
            return response()->json(['message' => 'Invitation does not belong to this board'], 422);
        }

        if (!$invitation->isPending()) {
            return response()->json(['message' => 'Can only cancel pending invitations'], 422);
        }

        $invitation->delete();

        return response()->json([
            'message' => 'Invitation was cancelled successfully',
        ]);
    }

    /**
     * Get invitation details.
     */
    public function showInvitation(BoardUserInvitation $invitation) {
        Gate::authorize('show', [BoardUserInvitation::class, $invitation]);
        return new BoardInvitationResource($invitation);
    }

    /**
     * Accept an invitation.
     */
    public function acceptInvitation(BoardUserInvitation $invitation) {
        Gate::authorize('accept', [BoardUserInvitation::class, $invitation]);

        $invitation->board->users()->attach(
            request()->user()->id, 
            [
            'board_id' => $invitation->board->id,
            'user_id' => request()->user()->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Invitation was accepted successfully',
            'board' => new BoardResource($invitation->board),
        ]);
    }

    /**
     * Decline an invitation.
     */
    public function declineInvitation(BoardUserInvitation $invitation) {
        Gate::authorize('decline', [BoardUserInvitation::class, $invitation]);

        $invitation->update(['status' => 'declined']);

        return response()->json([
            'message' => 'Invitation was declined successfully',
        ]);
    }

    /**
     * Get all the invitations of a board. For invitation management purposes only.
     */
    public function getBoardInvitations(Board $board) {
        Gate::authorize('manageInvitations', [BoardUserInvitation::class, $board]);
        return BoardInvitationResource::collection($board->invitations);
    }

    /**
     * Get all the invitations of a board. For invitation management purposes only.
     */
    public function getUserInvitations(Request $request) {
        $invitations = $request->query('sent') === 'true' 
            ? $request->user()->sentInvitations
            : $request->user()->receivedInvitations;
        
        return BoardInvitationResource::collection(
            $invitations
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
        );
    }

    /**
     * Leave a board.
     */
    public function leaveBoard(Board $board) {
        Gate::authorize('leave', [Board::class, $board]);
        $user = request()->user();

        $board->users()->detach(
            $user->id
        );

        BoardUserInvitation
            ::where('board_id', $board->id)
            ->where('user_id', $user->id)
            ->first()
            ->delete();

        return response()->json([
            'message' => 'Left board successfully',
        ]);
    }

    public function kickUser(Board $board, User $user) {
        Gate::authorize('manageMembers', [Board::class, $board]);

        $board->users()->detach(
            $user->id
        );

        BoardUserInvitation
            ::where('board_id', $board->id)
            ->where('user_id', $user->id)
            ->first()
            ->delete();

        return response()->json([
            'message' => 'Kicked user from board successfully',
        ]);
    }

    public function setRole(Board $board, User $user) {
        Gate::authorize('manageMembers', [Board::class, $board]);

        $validated = request()->validate(['role' => Rule::in(['admin', 'member'])]);

        $board->users()->updateExistingPivot($user->id, $validated);

        return response()->json([
            'message' => 'Member role updated successfully',
        ]);
    }

}
