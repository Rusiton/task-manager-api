<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Auth\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardInvitationResource extends JsonResource
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
            'board' => new BoardResource($this->board),
            'invitedUser' => new UserResource($this->user),
            'invitedBy' => new UserResource($this->invitedBy),
            'status' => $this->status,
            'expiresAt' => $this->expires_at,
        ];
    }
}
