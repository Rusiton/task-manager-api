<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreColumnRequest;
use App\Http\Requests\Api\V1\UpdateColumnRequest;
use App\Http\Resources\Api\V1\ColumnResource;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class ColumnController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }



    public function index(Request $request) {
        $validated = $request->validate([
            'boardId' => ['required', 'integer', 'exists:boards,id'],
        ]);

        $board = Board::find($validated['boardId']);

        Gate::authorize('show', [Column::class, $board]);

        return ColumnResource::collection($board->columns);
    }



    public function show(Column $column) {
        Gate::authorize('show', [Column::class, $column->board]);

        return new ColumnResource($column);
    }



    public function store(StoreColumnRequest $request) {
        $validated = $request->validated();

        $board = Board::find($validated['boardId']);

        Gate::authorize('create', [Column::class, $board]);

        $column = $board->columns()->create([
            'name' => $validated['name'],
            'position' => $validated['position'],
        ]);

        $column->board_id = $board->id;

        return new ColumnResource($column);
    }



    public function update(UpdateColumnRequest $request, Column $column) {
        $validated = $request->validated();

        Gate::authorize('modify', $column);

        $column->update($validated);

        return new ColumnResource($column);
    }



    public function destroy(Column $column) {
        Gate::authorize('modify', $column);

        $column->delete();

        return ['message' => 'Column was deleted successfully'];
    }
}
