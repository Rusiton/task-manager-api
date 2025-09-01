<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBoardRequest;
use App\Http\Requests\Api\V1\UpdateBoardRequest;
use App\Http\Resources\Api\V1\BoardCollection;
use App\Http\Resources\Api\V1\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class BoardController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),    
        ];
    }



    public function index(Request $request) {
        [$ownedBoards, $joinedBoards] = [
            $request->user()->owned_boards, 
            $request->user()->joined_boards
        ];

        return [
            'ownedBoards' => new BoardCollection($ownedBoards),
            'joinedBoards' => new BoardCollection($joinedBoards)
        ];
    }



    public function show(Board $board) {
        $includeColumns = request()->query('includeColumns');

        if ($includeColumns == 'true') {
            $board = $board->loadMissing('columns');
        }

        return new BoardResource($board);
    }



    public function store(StoreBoardRequest $request) {
        $validated = $request->validated();

        $board = $request->user()->owned_boards()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'owner_id' => $validated['ownerId'],
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
}
