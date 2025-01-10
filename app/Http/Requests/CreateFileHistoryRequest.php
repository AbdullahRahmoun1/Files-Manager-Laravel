<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\FileHistoryRules;

class CreateFileHistoryRequest extends FormRequest
{
    public function rules()
    {
        return FileHistoryRules::required()
            ->getRules();
    }

    public function messages(){
        return FileHistoryRules::getMessages();
    }
}
