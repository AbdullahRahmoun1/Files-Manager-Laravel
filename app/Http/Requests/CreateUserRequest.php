<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\UserRules;

class CreateUserRequest extends FormRequest
{
    public function rules()
    {
        return UserRules::required()
            ->getRules();
    }

    public function messages(){
        return UserRules::getMessages();
    }
}
