<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupRules;

class UpdateGroupRequest extends FormRequest
{
    public function rules()
    {
        return GroupRules::getRules();
    }

    public function messages()
    {
        return GroupRules::getMessages();
    }
}
