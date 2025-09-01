<?php

namespace App\Http\Resources\Api\V1;

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
            'columnId' => $this->column_id,
            'assignedTo' => $this->assigned_to,
            'name' => $this->name,
            'description' => $this->description,
            'position' => $this->position,
            'dueDate' => $this->due_date,
        ];
    }
}
