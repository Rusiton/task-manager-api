<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\Boards\TaskMovedWithinColumn;
use App\Events\Boards\TaskCreated;
use App\Events\Boards\TaskDeleted;
use App\Events\Boards\TaskMovedToColumn;
use App\Events\Boards\TaskUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\MoveTaskToColumnRequest;
use App\Http\Requests\Api\V1\MoveTaskToEmptyColumnRequest;
use App\Http\Requests\Api\V1\MoveTaskWithinColumnRequest;
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

        event(new TaskCreated($task));

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
        $prevTask = $task;

        $task->update([
            'column_id' => $column->id, 
            ...$validated,
        ]);

        if ($prevTask->column->token === $validated['column_token']) {
            event(new TaskUpdated($task->load('column')));
        }


        return new TaskResource($task->load('column')); // Returns updated task with reloaded relationship.
    }



    public function moveInsideColumn(MoveTaskWithinColumnRequest $request){
        $validated = $request->validated();

        $draggedTask = Task::where('token', $validated['draggedTask'])->first();
        $droppedOnTask = Task::where('token', $validated['droppedOnTask'])->first();

        $column = $draggedTask->column;

        DB::transaction(function () use ($draggedTask, $droppedOnTask, $column) {
            $previousPosition = $draggedTask->position;
            $newPosition = $droppedOnTask->position;

            // Sets position to negative to avoid constraint violations.
            $draggedTask->update([
                'position' => - ($draggedTask->position),
            ]);

            if ($previousPosition < $newPosition) {
                Task::where('column_id', $column->id)
                    ->whereKeyNot($draggedTask->id)
                    ->whereBetween('position', [$previousPosition + 1, $newPosition])
                    ->orderBy('position', 'asc')
                    ->decrement('position');
            }
            else {
                Task::where('column_id', $column->id)
                    ->whereKeyNot($draggedTask->id)
                    ->whereBetween('position', [$newPosition, $previousPosition - 1])
                    ->orderBy('position', 'desc')
                    ->increment('position');
            }

            $draggedTask->update([
                'position' => $newPosition,
            ]);
        });

        event(new TaskMovedWithinColumn($column->load('tasks')));

        return response()->json([
            'message' => 'Task moved within column successfully.',
        ]);
    }



    public function moveToEmptyColumn(MoveTaskToEmptyColumnRequest $request) {
        $validated = $request->validated();

        $task = Task::where('token', $validated['task'])->first();
        $column = Column::where('token', $validated['column'])->first();

        Gate::authorize('modify', [Task::class, $column->board]);

        $previousColumn = $task->column;
        $previousPosition = $task->position;

        DB::transaction(function () use ($task, $column, $previousColumn, $previousPosition) {
            $task->update([
                'column_id' => $column->id,
                'position' => 1,
            ]);

            Task::where('column_id', $previousColumn->id)
                ->where('position', '>', $previousPosition)
                ->decrement('position');
        });

        event(new TaskMovedToColumn($task, $previousColumn, $previousPosition));

        return response()->json([
            'message' => 'Task moved to empty column successfully.',
        ]);
    }



    public function moveToColumn(MoveTaskToColumnRequest $request) {
        $validated = $request->validated();

        $draggedTask = Task::where('token', $validated['draggedTask'])->first();
        $droppedOnTask = Task::where('token', $validated['droppedOnTask'])->first();

        Gate::authorize('modify', [Task::class, $draggedTask->column->board]);

        $previousColumn = $draggedTask->column;
        $previousPosition = $draggedTask->position;

        DB::transaction(function () use ($draggedTask, $droppedOnTask, $previousColumn, $previousPosition) {
            $newPos = $droppedOnTask->position;

            /**
             * Re-order tasks in the new column to insert the new dragged task.
             */
            Task::where('column_id', $droppedOnTask->column->id)
                ->where('position', '>', $newPos)
                ->orderBy('position', 'desc')
                ->increment('position');

            $droppedOnTask->increment('position');
            
            $draggedTask->update([
                'column_id' => $droppedOnTask->column->id,
                'position' => $newPos, 
            ]);

            /**
             * Re-order tasks in the previous column.
             */
            Task::where('column_id', $previousColumn->id)
                ->where('position', '>', $previousPosition)
                ->decrement('position');
        });

        event(new TaskMovedToColumn($draggedTask, $previousColumn, $previousPosition));

        return response()->json([
            'message' => 'Task moved to column successfully.',
        ]);

    }



    public function destroy(Task $task) {
        Gate::authorize('modify', [Task::class, $task->column->board]);

        $columnId = $task->column_id;
        $taskPosition = $task->position;

        $eventData = [
            'boardToken' => $task->column->board->token,
            'columnToken' => $task->column->token,
            'taskToken' => $task->token,
        ];

        $task->delete();

        Task::where('column_id', $columnId)
            ->where('position', '>', $taskPosition)
            ->decrement('position');
        
        event(new TaskDeleted(
            $eventData['boardToken'],
            $eventData['columnToken'],
            $eventData['taskToken'],
        ));

        return response()->json(['message' => 'Task was deleted successfully']);
    }
}
