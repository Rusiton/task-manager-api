<?php

namespace App\Events\Boards;

use App\Http\Resources\Api\V1\ColumnResource;
use App\Models\Column;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ColumnUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Column $column;

    /**
     * Create a new event instance.
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->column->board->token),
        ];
    }

    public function broadcastAs()
    {
        return 'column.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'column' => new ColumnResource($this->column),
        ];
    }
}
