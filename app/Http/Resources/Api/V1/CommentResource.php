<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Auth\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'content' => $this->content,
        ];
    }
}
