<?php

namespace App\Http\Resources;

use Wever\Laradot\App\Traits\ResourcePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
{
    use ResourcePaginator;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invitation_expires_at' => $this->invitation_expires_at,
            'joined_at' => $this->joined_at,
            'inviter_id' => $this->inviter_id,
            'group_id'=> $this->group_id,
            'user_id' => $this->user_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'inviter' => UserResource::make($this->whenLoaded('inviter')),
            'group' => GroupResource::make($this->whenLoaded('group')),
        ];
    }
}
