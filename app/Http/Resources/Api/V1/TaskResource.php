<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Auth\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'columnToken' => $this->column->token,
            'assignedTo' => new UserResource($this->assignedTo),
            'name' => $this->name,
            'description' => $this->description,
            'position' => $this->position,
            'dueDate' => $this->due_date,
        ];
    }
}
