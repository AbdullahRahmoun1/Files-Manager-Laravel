<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupFileRules;

class CreateGroupFileRequest extends FormRequest
{
    public function rules()
    {
        return GroupFileRules::required()
            ->getRules();
    }

    public function messages(){
        return GroupFileRules::getMessages();
    }
}
