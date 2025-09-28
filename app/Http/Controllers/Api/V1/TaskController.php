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

        if ($validated['assignedTo']) {
            $user = User::where('token', $validated['assignedTo'])->first();
            $validated['assignedTo'] = $user->id; // Replaces user token by user ID
        }
        
        $column = Column::where('token', $validated['columnToken'])->first();

        $task->update([
            'column_id' => $column->id,
            'assigned_to' => $validated['assignedTo'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'position' => $validated['position'],
            'due_date' => $validated['dueDate'],
        ]);

        return new TaskResource($task);
    }



    public function swapPositions(Request $request){
        $validated = $request->validate([
            'changedTaskToken' => ['required', 'exists:tasks,token'],
            'replacedTaskToken' => ['required', 'exists:tasks,token'],
        ]);

        DB::transaction(function () use ($validated) {
            $changedTask = Task::where('token', $validated['changedTaskToken'])->first();
            $replacedTask = Task::where('token', $validated['replacedTaskToken'])->first();

            Gate::authorize('modify', [Task::class, $changedTask->column->board]);

            $newPos = $replacedTask->position;

            $changedTask->position = -1; // Temp position to avoid constraint violation.
            $replacedTask->position = $changedTask->getOriginal('position');

            $changedTask->save(); // Updates position to avoid constraint violation.
            $replacedTask->save();

            $changedTask->update(['position' => $newPos]);
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
