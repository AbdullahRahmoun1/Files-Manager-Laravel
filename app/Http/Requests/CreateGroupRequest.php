<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\GroupRules;

class CreateGroupRequest extends FormRequest
{
    public function rules()
    {
        return GroupRules::required('creator_id')
            ->getRules();
    }

    public function messages(){
        return GroupRules::getMessages();
    }
}
