<?php

namespace App\Policies\Api\V1;

use App\Models\Board;
use App\Models\Column;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ColumnPolicy
{
    public function show(User $user, Board $board): Response
    {
        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You are not allowed to get this resource');
    }



    public function create(User $user, Board $board): Response
    {

        if ($user->id === $board->owner_id) {
            return Response::allow();
        }

        if ($board->users->contains($user) && $board->users->firstWhere('id', $user->id)->pivot->role == 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not posses the perms to make this action.');
    }



    public function modify(User $user, Column $column): Response
    {
        if ($user->id === $column->board->owner_id) {
            return Response::allow();
        }

        if ($column->board->users->contains($user) && $column->board->users->firstWhere('id', $user->id)->pivot->role == 'admin') {
            return Response::allow();
        }

        return Response::deny('You do not posses the perms to make this action.');
    }
}
