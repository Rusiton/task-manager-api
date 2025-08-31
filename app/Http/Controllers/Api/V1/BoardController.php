<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BoardRequest;
use App\Http\Resources\Api\V1\BoardCollection;
use App\Http\Resources\Api\V1\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BoardController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),    
        ];
    }



    public function index(Request $request) {
        // $boards = $request->user()->boards;

        $boards = Board::all();
        return new BoardCollection($boards);
    }



    public function show(Board $board) {
        $includeUsers = request()->query('includeUsers');
        $includeColumns = request()->query('includeColumns');

        if ($includeUsers == 'true') {
            $board = $board->loadMissing('users');
        }

        if ($includeColumns == 'true') {
            $board = $board->loadMissing('columns');
        }

        return new BoardResource($board);
    }



    public function store(BoardRequest $board) {

    }



    public function update(BoardRequest $board) {

    }



    public function destroy(Board $board) {

    }
}
