<?php

namespace App\Http\Resources;

use Wever\Laradot\App\Traits\ResourcePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class FileResource extends JsonResource
{
    use ResourcePaginator;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'extension' => $this->extension,
            'parent_id' => $this->parent_id,
            'creator_id' => $this->creator_id,
            'creator' => UserResource::collection($this->whenLoaded('creator')),
            'inGroups' => GroupResource::collection($this->whenLoaded('inGroups')),
            'parent' => FileResource::make($this->whenLoaded('parent')),
            'directChildren' => FileResource::collection($this->whenLoaded('directChildren')),
            'children' => FileResource::collection($this->whenLoaded('children')),
            'date_time' => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
