<?php

namespace App\Http\Resources;

use Wever\Laradot\App\Traits\ResourcePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInResource extends JsonResource
{
    use ResourcePaginator;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_id' => $this->file_id,
            'user_id' => $this->user_id,
            'checked_in_at' => $this->checked_in_at,
            'checked_out_at' => $this->checked_out_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'file' => FileResource::make($this->whenLoaded('file'))
        ];
    }
}
