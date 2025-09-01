<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function show(User $user, Board $board): Response
    {
        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You are not allowed to get this resource');
    }



    public function store(User $user, Board $board): Response
    {
        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You do not have the perms to make this action');
    }



    public function modify(User $user, Board $board): Response
    {
        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You do not have the perms to make this action');
    }
}
