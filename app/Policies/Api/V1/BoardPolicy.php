<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function modify(User $user, Board $board)
    {
        if ($user->id == $board->owner_id) return Response::allow();
        else return Response::deny('You do not posses the perms to make this action');
    }
}
