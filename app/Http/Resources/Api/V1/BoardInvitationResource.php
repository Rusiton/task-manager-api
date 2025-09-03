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
            'boardId' => $this->board_id,
            'userId' => $this->user_id,
            'invitedBy' => new UserResource(User::find($this->invited_by)),
            'invitation_url' => "invitations/{$this->token}",
            'status' => $this->status,
            'expiresAt' => $this->expires_at,
        ];
    }
}
