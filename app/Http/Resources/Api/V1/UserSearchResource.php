<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Auth\V1\UserProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
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
            'email' => $this->email,
            'name' => $this->name,
            'profile' => new UserProfileResource($this->profile),
        ];
    }
}
