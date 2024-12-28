<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupUserRules;

class CreateGroupUserRequest extends FormRequest
{
    public function rules()
    {
        return GroupUserRules::required()
            ->getRules();
    }

    public function messages(){
        return GroupUserRules::getMessages();
    }
}
