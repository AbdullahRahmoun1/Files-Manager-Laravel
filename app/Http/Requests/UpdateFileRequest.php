<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\FileRules;

class UpdateFileRequest extends FormRequest
{
    public function rules()
    {
        return FileRules::getRules();
    }

    public function messages(){
        return FileRules::getMessages();
    }
}
