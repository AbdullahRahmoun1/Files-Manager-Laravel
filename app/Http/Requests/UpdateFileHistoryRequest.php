<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\FileHistoryRules;

class UpdateFileHistoryRequest extends FormRequest
{
    public function rules()
    {
        return FileHistoryRules::getRules();
    }

    public function messages(){
        return FileHistoryRules::getMessages();
    }
}
