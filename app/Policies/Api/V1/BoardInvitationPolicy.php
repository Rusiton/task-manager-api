<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\BoardUserInvitation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardInvitationPolicy
{
    public function manageInvitations(User $user, Board $board): Response
    {
        return $user->id === $board->owner_id || $board->admins->contains($user)
            ? Response::allow()
            : Response::deny('Unallowed to get this resource');
    }



    public function invite(User $user, Board $board): Response
    {
        return $board->users->contains($user)
            ? Response::deny('User already belongs to this board')
            : Response::allow();
    }



    public function show(User $user, BoardUserInvitation $invitation): Response
    {
        return $invitation->user_id === $user->id 
            || $user->id === $invitation->board->owner_id 
            || $invitation->board->admins->contains($user)
            ? Response::allow()
            : Response::deny('User ID does not match invited User ID');
    }



    public function accept(User $user, BoardUserInvitation $invitation): Response
    {
        if ($invitation->isExpired()) {
            return Response::deny('Invitation has expired.');
        }

        if (!$invitation->isPending()) {
            return Response::deny('Invitation is no longer valid.');
        }

        return $invitation->board->users->contains($user)
            ? Response::deny('User already belongs to this board')
            : Response::allow();
    }



    public function decline(User $user, BoardUserInvitation $invitation): Response
    {
        if ($invitation->status !== 'pending') {
            return Response::deny('Invitation is either accepted, declined or already expired');
        }

        return $invitation->board->users->contains($user)
            ? Response::deny('User already belongs to this board')
            : Response::allow();
    }
}
