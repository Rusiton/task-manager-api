<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\Api\V1\TaskCollection;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }



    public function index(Request $request) {
        $validated = $request->validate([
            'columnId' => ['required', 'integer', 'exists:columns,id'],
        ]);

        $column = Column::find($validated['columnId']);

        Gate::authorize('show', [Task::class, $column->board]);

        return new TaskCollection($column->tasks);
    }

    

    public function show(Task $task) {
        Gate::authorize('show', [Task::class, $task->column->board]);

        return new TaskResource($task);
    }



    public function store(StoreTaskRequest $request) {
        $validated = $request->validated();
        $column = Column::find($validated['columnId']); 

        Gate::authorize('store', [Task::class, $column->board]);

        $task = $column->tasks()->create([
            'column_id' => $validated['columnId'],
            'assigned_to' => $validated['assignedTo'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'position' => $validated['position'],
            'due_date' => $validated['dueDate'],
        ]);

        return new TaskResource($task);
    }



    public function update(UpdateTaskRequest $request, Task $task) {
        $validated = $request->validated();

        Gate::authorize('modify', [Task::class, $task->column->board]);

        $task->update($validated);

        return new TaskResource($task);
    }



    public function destroy(Task $task) {
        Gate::authorize('modify', [Task::class, $task->column->board]);

        $task->delete();

        return ['message' => 'Task was deleted successfully'];
    }
}
