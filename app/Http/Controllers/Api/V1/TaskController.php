<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
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
            'columnToken' => ['required', 'exists:columns,token'],
        ]);

        $column = Column::where('token', $validated['columnToken'])->first();

        Gate::authorize('show', [Task::class, $column->board]);

        return TaskResource::collection($column->tasks);
    }

    

    public function show(Task $task) {
        Gate::authorize('show', [Task::class, $task->column->board]);

        return new TaskResource($task);
    }



    public function store(StoreTaskRequest $request) {
        $validated = $request->validated();

        $column = Column::where('token', $validated['columnToken'])->first(); 
        $assignedTo = User::where('token', $validated['assignedTo'])->first();

        Gate::authorize('store', [Task::class, $column->board]);

        $task = $column->tasks()->create([
            'column_id' => $column->id,
            'assigned_to' => $assignedTo ? $assignedTo->id: null,
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

        if (array_key_exists('assigned_to', $validated)) {
            $user = User::where('token', $validated['assigned_to'])->first();
            $validated['assigned_to'] = $user->id; // Replaces user token by user ID
        }
        
        $column = Column::where('token', $validated['column_token'])->first();

        $task->update([
            'column_id' => $column->id, 
            ...$validated,
        ]);

        return new TaskResource($task->load('column')); // Returns updated task with reloaded relationship.
    }



    public function orderPositions(Request $request){
        $validated = $request->validate([
            'columnToken' => ['required', 'exists:columns,token'],
            'orderedTasksTokens' => ['required', 'array'],
            'orderedTasksTokens.*' => ['required', 'exists:tasks,token'],
        ]);

        DB::transaction(function () use ($validated) {
            $column = Column::where('token', $validated['columnToken'])->first();

            $tasks = Task::where('column_id', $column->id)->get();
            $tempStart = -(10**5); // High temporary position to avoid constraint violation.

            foreach ($tasks as $index => $task) {
                $task->update(['position' => $tempStart + $index]);
            }

            foreach ($validated['orderedTasksTokens'] as $position => $token) {
                Task::where('token', $token)->update(['position' => $position + 1]); // Updates each task position
            }
        });

        return response()->json([
            'message' => 'Positions were swapped successfully.',
        ]);
    }



    public function destroy(Task $task) {
        Gate::authorize('modify', [Task::class, $task->column->board]);

        $columnId = $task->column_id;
        $taskPosition = $task->position;

        $task->delete();

        Task::where('column_id', $columnId)
            ->where('position', '>', $taskPosition)
            ->decrement('position');

        return response()->json(['message' => 'Task was deleted successfully']);
    }
}
