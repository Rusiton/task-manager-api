<?php

namespace App\Events\Boards;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $boardToken;
    public string $columnToken;
    public string $taskToken;

    /**
     * Create a new event instance.
     */
    public function __construct(string $boardToken, string $columnToken, string $taskToken)
    {
        $this->boardToken = $boardToken;
        $this->columnToken = $columnToken;
        $this->taskToken = $taskToken;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->boardToken),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'column' => $this->columnToken,
            'task' => $this->taskToken,
        ];
    }
}
