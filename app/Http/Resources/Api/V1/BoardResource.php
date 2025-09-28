<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Auth\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BoardResource extends JsonResource
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
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'owner' => new UserResource($this->user),
            'admins' => UserResource::collection($this->admins),
            'members' => UserResource::collection($this->users),
            'lists' => ColumnResource::collection($this->columns->sortBy('position')),
        ];
    }
}
