<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\UserRules;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        return UserRules::getRules();
    }

    public function messages(){
        return UserRules::getMessages();
    }
}
