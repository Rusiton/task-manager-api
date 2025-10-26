<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function show(User $user, Board $board): Response
    {
        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You are not allowed to get this resource');
    }



    public function modify(User $user, Board $board): Response
    {
        return $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You do not posses the perms to modify this');
    }

    public function leave(User $user, Board $board)
    {
        return $board->users->contains($user)
            ? Response::allow()
            : Response::deny('User does not belong to this board');
    }

    public function manageMembers(User $user, Board $board)
    {
        if ($user->id === $board->owner_id) {
            return Response::allow();
        }

        if ($board->users->contains($user) && $board->users->firstWhere('id', $user->id)->pivot->role == 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not posses the perms to make this action.');
    }
}
