<?php

namespace App\Http\Resources;

use App\Models\File;
use DateTime;
use Wever\Laradot\App\Traits\ResourcePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class FileHistoryResource extends JsonResource
{
    use ResourcePaginator;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'path' => $this->path,
            'comparison' => $this->comparison,
            'file_id' => $this->file_id,
            'check_in_id' => $this->check_in_id,
            'file' => FileResource::make($this->whenLoaded('file')),
            'check_in' => CheckInResource::make($this->whenLoaded('checkIn')),
            'date_time' => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
