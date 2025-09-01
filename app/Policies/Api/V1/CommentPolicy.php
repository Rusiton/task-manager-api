<?php

namespace App\Policies\Api\V1;

use App\Models\Comment;
use App\Models\Interfaces\Commentable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function store(User $user, $commentableType, $commentableId) {
        $modelClass = "App\\Models\\{$commentableType}";
        $commentable = $modelClass::find($commentableId);

        if (!$commentable instanceof Commentable) {
            return Response::deny('This model cannot be commented');
        }

        $board = $commentable->getBoard();

        return $board->users->contains($user) || $user->id === $board->owner_id
            ? Response::allow()
            : Response::deny('You do not have the perms to make this action');
    }



    public function modify(User $user, Comment $comment) {
        $modelClass = $comment->commentable_type;
        $commentable = $modelClass::find($comment->commentable_id);
        
        $board = $commentable->getBoard();

        if ($user->id === $comment->user_id 
            || $user->id === $board->owner_id 
            || $board->admins->contains($user)) {
            return Response::allow();
        }

        return Response::deny('You do not have the perms to make this action');
    }
}
