<?php

namespace App\Http\Resources\Auth\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $accessToken;

    public function __construct($resource, $accessToken = null)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $propsArray = [
            'token' => $this->token,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new UserProfileResource($this->profile),
            'settings' => new UserSettingsResource($this->settings),
        ];

        if (!empty($this->accessToken)) {
            $propsArray['accessToken'] = $this->accessToken;
        }

        return $propsArray;
    }
}
