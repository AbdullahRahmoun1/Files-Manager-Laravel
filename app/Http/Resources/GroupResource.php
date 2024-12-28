<?php

namespace App\Http\Resources;

use Wever\Laradot\App\Traits\ResourcePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    use ResourcePaginator;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'lang' => $this->lang,
            'creator_id' => $this->creator_id,
            'creator' => UserResource::make($this->whenLoaded('creator'))
        ];
    }
}
