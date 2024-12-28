<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupUserRules;

class UpdateGroupUserRequest extends FormRequest
{
    public function rules()
    {
        return GroupUserRules::getRules();
    }

    public function messages(){
        return GroupUserRules::getMessages();
    }
}
