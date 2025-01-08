<?php

namespace App\Http\Requests;

use App\Enums\GroupFileStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class DecideFileStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'group_id' => ['required','exists:groups,id'],
            'file_id' => ['required','exists:files,id'],
            'status' => ['required','in:'.implode(',',GroupFileStatusEnum::values())]
        ];
    }
}
