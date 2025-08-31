<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function modify(User $user, Board $board): Response
    { 
        return $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny("You don't have the perms to modify this.");
    }
}
