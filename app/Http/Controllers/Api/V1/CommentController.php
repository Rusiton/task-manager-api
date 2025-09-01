<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Requests\Api\V1\UpdateCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }



    public function show(Comment $comment) {
        return new CommentResource($comment);
    }



    public function store(StoreCommentRequest $request) {
        $validated = $request->validated();

        $type = $validated['commentableType'];
        $id = $validated['commentableId'];
        
        Gate::authorize('store', [Comment::class, $type, $id]);

        $modelClass = "App\\Models\\{$type}";
        if (!class_exists($modelClass)) { 
            return response()->json(['error' => 'Invalid model type'], 400); 
        }
        
        $model = $modelClass::find($id);
        if (!$model) { 
            return response()->json(['error' => 'Model not found'], 404); 
        }

        $comment = $model->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return new CommentResource($comment);
    }



    public function update(UpdateCommentRequest $request, Comment $comment) {
        $validated = $request->validated();

        Gate::authorize('modify', $comment);

        $comment->update($validated);

        return new CommentResource($comment);
    }



    public function destroy(Comment $comment) {
        Gate::authorize('modify', $comment);

        $comment->delete();

        return ['message' => 'Comment was deleted successfully'];
    }
}
