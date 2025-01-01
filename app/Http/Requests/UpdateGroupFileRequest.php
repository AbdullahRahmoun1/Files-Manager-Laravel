<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupFileRules;

class UpdateGroupFileRequest extends FormRequest
{
    public function rules()
    {
        return GroupFileRules::getRules();
    }

    public function messages(){
        return GroupFileRules::getMessages();
    }
}
