<?php

namespace App\Events\Boards;

use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMovedToColumn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Task $task, public Column $previousColumn, public int $previousPosition)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->task->column->board->token),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.movedToColumn';
    }

    public function broadcastWith(): array
    {
        return [
            'task' => new TaskResource($this->task),
            'previousColumn' => $this->previousColumn->token,
            'previousPosition' => $this->previousPosition,
        ];
    }
}
