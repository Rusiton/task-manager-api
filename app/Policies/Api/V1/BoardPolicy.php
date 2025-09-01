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
}
